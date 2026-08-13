#!/usr/bin/env python3
"""Render the Mermaid blocks from the editorial manual as high-resolution PNGs."""

from __future__ import annotations

import json
import re
import shutil
import subprocess
import sys
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "docs" / "README-ADMIN-WORDPRESS.md"
OUTPUT = ROOT / "docs" / "assets" / "admin-guide" / "diagrams"
WORK = ROOT / "tmp" / "pdfs" / "manual-mermaid"


def extract_blocks(text: str) -> list[str]:
    return [match.strip() + "\n" for match in re.findall(r"~~~mermaid\s*\n(.*?)\n~~~", text, flags=re.DOTALL)]


def main() -> None:
    blocks = extract_blocks(SOURCE.read_text(encoding="utf-8"))
    if not blocks:
        raise RuntimeError("No Mermaid blocks found in the editorial manual")

    npx = shutil.which("npx.cmd") or shutil.which("npx")
    if not npx:
        raise RuntimeError("npx is required to render the manual diagrams")

    OUTPUT.mkdir(parents=True, exist_ok=True)
    WORK.mkdir(parents=True, exist_ok=True)
    config = WORK / "mermaid-config.json"
    config.write_text(
        json.dumps(
            {
                "theme": "base",
                "securityLevel": "strict",
                "fontFamily": "Arial, Helvetica, sans-serif",
                "themeVariables": {
                    "primaryColor": "#f3f1eb",
                    "primaryTextColor": "#090909",
                    "primaryBorderColor": "#090909",
                    "lineColor": "#b5121b",
                    "secondaryColor": "#ffffff",
                    "tertiaryColor": "#fff4f4",
                    "actorBkg": "#f3f1eb",
                    "actorBorder": "#090909",
                    "actorTextColor": "#090909",
                    "signalColor": "#b5121b",
                    "signalTextColor": "#090909",
                    "labelBoxBkgColor": "#ffffff",
                    "labelBoxBorderColor": "#b5121b",
                    "labelTextColor": "#090909",
                    "noteBkgColor": "#fff4f4",
                    "noteBorderColor": "#b5121b",
                },
                "flowchart": {"curve": "basis", "htmlLabels": False, "nodeSpacing": 34, "rankSpacing": 46},
                "sequence": {"diagramMarginX": 28, "diagramMarginY": 18, "actorMargin": 42, "messageMargin": 34},
            },
            ensure_ascii=False,
            indent=2,
        ),
        encoding="utf-8",
    )

    expected: set[Path] = set()
    for index, block in enumerate(blocks, start=1):
        source = WORK / f"diagram-{index:02d}.mmd"
        target = OUTPUT / f"manual-diagram-{index:02d}.png"
        source.write_text(block, encoding="utf-8")
        subprocess.run(
            [
                npx,
                "-y",
                "@mermaid-js/mermaid-cli",
                "--input",
                str(source),
                "--output",
                str(target),
                "--configFile",
                str(config),
                "--backgroundColor",
                "white",
                "--width",
                "1800",
                "--scale",
                "2",
                "--quiet",
            ],
            cwd=ROOT,
            check=True,
        )
        expected.add(target.resolve())

    for obsolete in OUTPUT.glob("manual-diagram-*.png"):
        if obsolete.resolve() not in expected:
            obsolete.unlink()

    print(f"rendered_diagrams={len(blocks)}")


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:
        print(f"diagram render failed: {exc}", file=sys.stderr)
        raise
