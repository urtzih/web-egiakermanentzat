#!/usr/bin/env python3
"""Build the illustrated WordPress editorial manual as a reproducible PDF."""

from __future__ import annotations

import re
import sys
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import mm
from reportlab.platypus import (
    HRFlowable,
    Image,
    KeepTogether,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "docs" / "README-ADMIN-WORDPRESS.md"
OUTPUT = ROOT / "output" / "pdf" / "manual-editorial-wordpress.pdf"
DIAGRAM_DIR = ROOT / "docs" / "assets" / "admin-guide" / "diagrams"


def inline(text: str) -> str:
    text = text.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
    text = re.sub(r"!\[([^]]*)\]\(([^)]+)\)", r"\1", text)
    text = re.sub(r"\[([^]]+)\]\(([^)]+)\)", r'<link href="\2" color="#b5121b">\1</link>', text)
    text = re.sub(r"`([^`]+)`", r'<font name="Courier">\1</font>', text)
    text = re.sub(r"\*\*([^*]+)\*\*", r"<b>\1</b>", text)
    return text


def page_footer(canvas, doc) -> None:
    canvas.saveState()
    canvas.setStrokeColor(colors.HexColor("#d9d9d9"))
    canvas.line(18 * mm, 14 * mm, 192 * mm, 14 * mm)
    canvas.setFont("Helvetica", 8)
    canvas.setFillColor(colors.HexColor("#555555"))
    canvas.drawString(18 * mm, 9 * mm, "Egia Kermanentzat · Manual editorial WordPress · 13/08/2026")
    canvas.drawRightString(192 * mm, 9 * mm, str(doc.page))
    canvas.restoreState()


def diagram_block(number: int, lines: list[str], styles, width: float) -> KeepTogether:
    path = DIAGRAM_DIR / f"manual-diagram-{number:02d}.png"
    if not path.is_file():
        raise RuntimeError(
            f"Missing rendered Mermaid diagram: {path}. "
            "Run scripts/render-admin-manual-diagrams.py first."
        )

    title = "SECUENCIA DEL AVISO POR EMAIL" if any(line.strip().startswith("sequenceDiagram") for line in lines) else "DIAGRAMA DE FLUJO"
    image = Image(str(path))
    max_height = 120 * mm if number == 5 else 150 * mm
    scale = min(width / image.imageWidth, max_height / image.imageHeight, 1)
    image.drawWidth = image.imageWidth * scale
    image.drawHeight = image.imageHeight * scale
    image.hAlign = "CENTER"
    return KeepTogether([
        Paragraph(title, styles["DiagramTitle"]),
        Spacer(1, 2 * mm),
        image,
        Spacer(1, 4 * mm),
    ])


def build() -> None:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    styles = getSampleStyleSheet()
    styles.add(ParagraphStyle(name="CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold", fontSize=29, leading=32, textColor=colors.HexColor("#090909"), spaceAfter=8 * mm))
    styles.add(ParagraphStyle(name="CoverSub", parent=styles["BodyText"], fontSize=13, leading=18, textColor=colors.HexColor("#444444"), spaceAfter=5 * mm))
    styles.add(ParagraphStyle(name="H1Manual", parent=styles["Heading1"], fontName="Helvetica-Bold", fontSize=20, leading=24, textColor=colors.HexColor("#090909"), spaceBefore=4 * mm, spaceAfter=4 * mm, keepWithNext=True))
    styles.add(ParagraphStyle(name="H2Manual", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=14, leading=18, textColor=colors.HexColor("#b5121b"), spaceBefore=5 * mm, spaceAfter=2.5 * mm, keepWithNext=True))
    styles.add(ParagraphStyle(name="H3Manual", parent=styles["Heading3"], fontName="Helvetica-Bold", fontSize=11, leading=14, spaceBefore=3.5 * mm, spaceAfter=2 * mm, keepWithNext=True))
    styles.add(ParagraphStyle(name="BodyManual", parent=styles["BodyText"], fontName="Helvetica", fontSize=9.4, leading=13.2, textColor=colors.HexColor("#1a1a1a"), spaceAfter=2.7 * mm))
    styles.add(ParagraphStyle(name="TableHeader", parent=styles["BodyManual"], fontName="Helvetica-Bold", fontSize=8.6, leading=10.5, textColor=colors.white, spaceAfter=0))
    styles.add(ParagraphStyle(name="TableCell", parent=styles["BodyManual"], fontSize=8.7, leading=11.2, spaceAfter=0))
    styles.add(ParagraphStyle(name="BulletManual", parent=styles["BodyManual"], leftIndent=4 * mm, firstLineIndent=0, spaceAfter=1.2 * mm))
    styles.add(ParagraphStyle(name="Caption", parent=styles["BodyText"], alignment=TA_CENTER, fontSize=8, leading=10, textColor=colors.HexColor("#555555"), spaceAfter=4 * mm))
    styles.add(ParagraphStyle(name="DiagramTitle", parent=styles["BodyText"], fontName="Helvetica-Bold", fontSize=8, textColor=colors.white, backColor=colors.HexColor("#b5121b"), borderPadding=4))
    styles.add(ParagraphStyle(name="Diagram", parent=styles["BodyManual"], fontSize=8.5, leading=12, backColor=colors.HexColor("#f3f1eb"), borderColor=colors.HexColor("#090909"), borderWidth=0.5, borderPadding=7, spaceAfter=2 * mm))

    doc = SimpleDocTemplate(str(OUTPUT), pagesize=A4, rightMargin=18 * mm, leftMargin=18 * mm, topMargin=18 * mm, bottomMargin=20 * mm, title="Manual editorial WordPress", author="Egia Kermanentzat Elkartea", subject="Operación editorial bilingüe y avisos Sender", invariant=1)
    story = [Spacer(1, 22 * mm), Paragraph("MANUAL EDITORIAL<br/>WORDPRESS", styles["CoverTitle"]), HRFlowable(width="100%", thickness=5, color=colors.HexColor("#e31b23")), Spacer(1, 8 * mm), Paragraph("Egia Kermanentzat Elkartea", styles["CoverSub"]), Paragraph("Acceso seguro a staging, publicación bilingüe, hemeroteca, fuentes, imágenes, programación y avisos por email.", styles["CoverSub"]), Spacer(1, 65 * mm), Paragraph("Versión 13/08/2026 · Plugin editorial 0.2.6", styles["CoverSub"]), PageBreak()]

    lines = SOURCE.read_text(encoding="utf-8").splitlines()
    index = 0
    section_count = 0
    diagram_count = 0
    while index < len(lines):
        line = lines[index].rstrip()
        if not line:
            index += 1
            continue
        if line.startswith("~~~mermaid"):
            block = []
            index += 1
            while index < len(lines) and not lines[index].startswith("~~~"):
                block.append(lines[index])
                index += 1
            diagram_count += 1
            story.append(diagram_block(diagram_count, block, styles, doc.width))
            index += 1
            continue
        image_match = re.fullmatch(r"!\[([^]]*)\]\(([^)]+)\)", line)
        if image_match:
            image_path = (SOURCE.parent / image_match.group(2)).resolve()
            if image_path.is_file():
                image = Image(str(image_path))
                max_w = 170 * mm
                max_h = 150 * mm if image_path.name.startswith("sender-") else 105 * mm
                scale = min(max_w / image.imageWidth, max_h / image.imageHeight, 1)
                image.drawWidth = image.imageWidth * scale
                image.drawHeight = image.imageHeight * scale
                image.hAlign = "CENTER"
                story.extend([image, Paragraph(inline(image_match.group(1)), styles["Caption"])])
            index += 1
            continue
        if line.startswith("# "):
            # The source title is represented by the dedicated cover.
            index += 1
            continue
        if line.startswith("## "):
            section_count += 1
            if section_count > 1:
                story.append(Spacer(1, 2 * mm))
            story.append(Paragraph(inline(line[3:]), styles["H1Manual"]))
            index += 1
            continue
        if line.startswith("### "):
            story.append(Paragraph(inline(line[4:]), styles["H2Manual"]))
            index += 1
            continue
        if line.startswith("#### "):
            story.append(Paragraph(inline(line[5:]), styles["H3Manual"]))
            index += 1
            continue
        if line.startswith("|") and index + 1 < len(lines) and re.match(r"^\|[-: |]+\|$", lines[index + 1]):
            rows = []
            while index < len(lines) and lines[index].startswith("|"):
                if not re.match(r"^\|[-: |]+\|$", lines[index]):
                    row_style = styles["TableHeader"] if not rows else styles["TableCell"]
                    rows.append([Paragraph(inline(cell.strip()), row_style) for cell in lines[index].strip("|").split("|")])
                index += 1
            widths = [doc.width / len(rows[0])] * len(rows[0])
            table = Table(rows, colWidths=widths, repeatRows=1, hAlign="LEFT")
            table.setStyle(TableStyle([("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#090909")), ("TEXTCOLOR", (0, 0), (-1, 0), colors.white), ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"), ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#8a8a8a")), ("VALIGN", (0, 0), (-1, -1), "TOP"), ("LEFTPADDING", (0, 0), (-1, -1), 5), ("RIGHTPADDING", (0, 0), (-1, -1), 5), ("TOPPADDING", (0, 0), (-1, -1), 4), ("BOTTOMPADDING", (0, 0), (-1, -1), 4)]))
            story.extend([table, Spacer(1, 4 * mm)])
            continue
        if re.match(r"^(?:- |\d+\. )", line):
            items = []
            while index < len(lines) and re.match(r"^(?:- |\d+\. )", lines[index]):
                items.append(ListItem(Paragraph(inline(re.sub(r"^(?:- |\d+\. )", "", lines[index])), styles["BulletManual"]), leftIndent=4 * mm))
                index += 1
            story.append(ListFlowable(items, bulletType="bullet", start="circle", leftIndent=6 * mm, bulletFontSize=7))
            story.append(Spacer(1, 2 * mm))
            continue
        if line.startswith("```"):
            code = []
            index += 1
            while index < len(lines) and not lines[index].startswith("```"):
                code.append(lines[index])
                index += 1
            story.append(Paragraph("<br/>".join(inline(row).replace(" ", "&nbsp;") for row in code), styles["Diagram"]))
            index += 1
            continue

        paragraph = [line]
        index += 1
        while index < len(lines) and lines[index].strip() and not re.match(r"^(#|\||-|\d+\. |!\[|~~~|```)", lines[index]):
            paragraph.append(lines[index].strip())
            index += 1
        story.append(Paragraph(inline(" ".join(paragraph)), styles["BodyManual"]))

    doc.build(story, onFirstPage=page_footer, onLaterPages=page_footer)
    print(OUTPUT)


if __name__ == "__main__":
    try:
        build()
    except Exception as exc:
        print(f"manual build failed: {exc}", file=sys.stderr)
        raise
