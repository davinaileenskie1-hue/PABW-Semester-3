import sys
from docx import Document

SRC = r"D:\xampp\htdocs\PABW\Praktikum_7\PRK_TA_7\707012500135_Davin Aileenskie Sofiandy_TA_modul7.docx"
doc = Document(SRC)

print("total paragraphs:", len(doc.paragraphs))
print("inline shapes:", len(doc.inline_shapes))
print("shape widths (unique):", sorted({s.width for s in doc.inline_shapes}))

print("\n--- cover (first 20) ---")
for i, p in enumerate(doc.paragraphs[:20]):
    print(i, repr(p.style.name), "bold=", (p.runs[0].bold if p.runs else None), "|", p.text[:100])

print("\n--- bold headings ---")
for i, p in enumerate(doc.paragraphs):
    if p.text.strip() and p.runs and p.runs[0].bold:
        print(i, "|", p.text[:110])

print("\n--- body images with preceding text ---")
from docx.oxml.ns import qn
for i, p in enumerate(doc.paragraphs):
    if p._element.findall('.//' + qn('pic:pic')):
        prev = doc.paragraphs[i - 1].text[:80] if i else ''
        print(i, "| after:", prev)
