import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent
manifest = json.loads((ROOT / "laporan-assets" / "manifest.json").read_text(encoding="utf-8"))

for item in manifest:
    p = ROOT / item["file"]
    text = p.read_text(encoding="utf-8")
    text = text.replace("\r\n", "\n")
    text = text.replace("\t", "    ")
    lines = [ln.rstrip() for ln in text.split("\n")]
    # rapatkan komentar inline: "//Pengguna" -> "// Pengguna"
    fixed = []
    for ln in lines:
        if "//" in ln and not ln.lstrip().startswith("//") and not ln.lstrip().startswith("*"):
            head, _, tail = ln.partition("//")
            if tail and not tail.startswith(" ") and not tail.startswith("/"):
                ln = head + "// " + tail
        fixed.append(ln)
    out = "\n".join(fixed).rstrip() + "\n"
    if out != text:
        p.write_text(out, encoding="utf-8")
        print("normalized", item["file"])
