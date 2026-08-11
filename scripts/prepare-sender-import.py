#!/usr/bin/env python3
"""Validate consent evidence and prepare a Sender-compatible CSV.

The report contains row numbers and reasons, never email addresses. The output
CSV is personal data and must remain in an ignored temporary directory.
"""

from __future__ import annotations

import argparse
import csv
import json
import re
import sys
import zipfile
from datetime import date, datetime
from pathlib import Path
from xml.etree import ElementTree as ET

REQUIRED = ("email", "consent_date", "consent_source", "consent_scope")
EMAIL_RE = re.compile(r"^[^\s@]+@[^\s@]+\.[^\s@]+$")
XLSX_NS = "{http://schemas.openxmlformats.org/spreadsheetml/2006/main}"


def column_index(reference: str) -> int:
    letters = "".join(char for char in reference if char.isalpha()).upper()
    value = 0
    for char in letters:
        value = value * 26 + ord(char) - 64
    return value - 1


def read_xlsx(path: Path) -> list[dict[str, str]]:
    with zipfile.ZipFile(path) as archive:
        shared: list[str] = []
        if "xl/sharedStrings.xml" in archive.namelist():
            root = ET.fromstring(archive.read("xl/sharedStrings.xml"))
            for item in root.findall(f"{XLSX_NS}si"):
                shared.append("".join(node.text or "" for node in item.iter(f"{XLSX_NS}t")))
        workbook = ET.fromstring(archive.read("xl/workbook.xml"))
        relationships = ET.fromstring(archive.read("xl/_rels/workbook.xml.rels"))
        targets = {rel.attrib["Id"]: rel.attrib["Target"] for rel in relationships}
        sheet = workbook.find(f"{XLSX_NS}sheets/{XLSX_NS}sheet")
        if sheet is None:
            return []
        rel_id = sheet.attrib["{http://schemas.openxmlformats.org/officeDocument/2006/relationships}id"]
        target = targets[rel_id].lstrip("/")
        target = target if target.startswith("xl/") else f"xl/{target}"
        root = ET.fromstring(archive.read(target))
        rows: list[list[str]] = []
        for row in root.findall(f".//{XLSX_NS}row"):
            values: list[str] = []
            for cell in row.findall(f"{XLSX_NS}c"):
                index = column_index(cell.attrib.get("r", "A1"))
                while len(values) <= index:
                    values.append("")
                value_node = cell.find(f"{XLSX_NS}v")
                value = value_node.text if value_node is not None and value_node.text else ""
                if cell.attrib.get("t") == "s" and value.isdigit():
                    value = shared[int(value)]
                elif cell.attrib.get("t") == "inlineStr":
                    value = "".join(node.text or "" for node in cell.iter(f"{XLSX_NS}t"))
                values[index] = value.strip()
            rows.append(values)
    if not rows:
        return []
    headers = [value.strip().lower() for value in rows[0]]
    return [{headers[index]: value for index, value in enumerate(row) if index < len(headers)} for row in rows[1:]]


def read_rows(path: Path) -> list[dict[str, str]]:
    if path.suffix.lower() == ".xlsx":
        return read_xlsx(path)
    with path.open("r", encoding="utf-8-sig", newline="") as handle:
        return [{key.strip().lower(): (value or "").strip() for key, value in row.items()} for row in csv.DictReader(handle)]


def parse_date(value: str) -> str | None:
    value = value.strip()
    for pattern in ("%Y-%m-%d", "%d/%m/%Y", "%d-%m-%Y"):
        try:
            return datetime.strptime(value, pattern).date().isoformat()
        except ValueError:
            pass
    if value.isdigit():
        try:
            return (date(1899, 12, 30) + __import__("datetime").timedelta(days=int(value))).isoformat()
        except (ValueError, OverflowError):
            pass
    return None


def read_suppressions(path: Path | None) -> set[str]:
    if path is None:
        return set()
    rows = read_rows(path)
    return {row.get("email", "").strip().lower() for row in rows if row.get("email")}


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("input", type=Path)
    parser.add_argument("--output", type=Path, required=True)
    parser.add_argument("--report", type=Path, required=True)
    parser.add_argument("--suppressions", type=Path)
    args = parser.parse_args()

    if not args.input.is_file():
        parser.error("input does not exist")
    rows = read_rows(args.input)
    if rows and any(field not in rows[0] for field in REQUIRED):
        parser.error("required columns: " + ", ".join(REQUIRED))

    suppressions = read_suppressions(args.suppressions)
    seen: set[str] = set()
    accepted: list[dict[str, str]] = []
    rejected: list[dict[str, object]] = []
    for number, row in enumerate(rows, start=2):
        email = row.get("email", "").strip().lower()
        consent_date = parse_date(row.get("consent_date", ""))
        reasons: list[str] = []
        if not EMAIL_RE.match(email):
            reasons.append("invalid_email")
        if not consent_date:
            reasons.append("invalid_consent_date")
        if not row.get("consent_source", "").strip():
            reasons.append("missing_consent_source")
        if not row.get("consent_scope", "").strip():
            reasons.append("missing_consent_scope")
        if row.get("status", "").strip().lower() in {"unsubscribed", "suppressed", "complaint", "bounced"} or email in suppressions:
            reasons.append("suppressed")
        if email in seen:
            reasons.append("duplicate")
        if reasons:
            rejected.append({"row": number, "reasons": reasons})
            continue
        seen.add(email)
        accepted.append({
            "email": email,
            "consent_date": consent_date or "",
            "consent_source": row["consent_source"].strip(),
            "consent_scope": row["consent_scope"].strip(),
        })

    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.report.parent.mkdir(parents=True, exist_ok=True)
    with args.output.open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=list(REQUIRED))
        writer.writeheader()
        writer.writerows(accepted)
    args.report.write_text(json.dumps({
        "input_rows": len(rows),
        "accepted": len(accepted),
        "rejected": len(rejected),
        "rejections": rejected,
    }, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"accepted={len(accepted)} rejected={len(rejected)}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
