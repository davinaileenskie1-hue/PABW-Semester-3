from pathlib import Path

from PIL import Image, ImageChops

BASE = Path(r"D:\xampp\htdocs\PABW\Praktikum_7")
SRC = BASE / "laporan-assets" / "screenshots"
DST = BASE / "laporan-assets" / "screenshots-crop"
DST.mkdir(exist_ok=True)

# Pages whose content sits in a narrow centred column, so the generic
# bottom-trim would leave a lot of empty space on both sides.
MANUAL = {
    "01-login-redirect.png": (430, 220, 1010, 700),
    "03-403.png": (330, 360, 1110, 540),
    # Chrome's image viewer letterboxes portrait images with black bars.
    "19-file-storage-url.png": (319, 0, 881, 800),
}

for f in sorted(SRC.glob("*.png")):
    if f.name.startswith("_"):
        continue
    im = Image.open(f).convert("RGB")
    w, h = im.size
    if f.name in MANUAL:
        out = im.crop(MANUAL[f.name])
        out.save(DST / f.name)
        print(f.name, im.size, "->", out.size, "(manual)")
        continue
    bg = im.getpixel((4, h - 4))
    # Row-by-row difference against the background colour.
    last = h - 1
    for y in range(h - 1, -1, -1):
        row = im.crop((0, y, w, y + 1))
        diff = ImageChops.difference(row, Image.new("RGB", row.size, bg)).convert("L")
        if diff.getextrema()[1] > 14:
            last = y
            break
    bottom = min(h, last + 56)
    bottom = max(bottom, 300)
    out = im.crop((0, 0, w, bottom))
    out.save(DST / f.name)
    print(f.name, im.size, "->", out.size)
