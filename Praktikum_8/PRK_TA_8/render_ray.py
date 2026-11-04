import json
import sys
from pathlib import Path

from playwright.sync_api import sync_playwright

ROOT = Path(__file__).resolve().parent
RAY_DIR = ROOT / "laporan-assets" / "ray"
RAY_DIR.mkdir(parents=True, exist_ok=True)
MANIFEST = ROOT / "laporan-assets" / "manifest.json"

URL = "https://ray.so/#background=false&padding=16&lineNumbers=true&language=php"

SET_VALUE = """
(el, code) => {
  const setter = Object.getOwnPropertyDescriptor(
    window.HTMLTextAreaElement.prototype, 'value').set;
  setter.call(el, code);
  el.dispatchEvent(new Event('input', { bubbles: true }));
}
"""


def main():
    manifest = json.loads(MANIFEST.read_text(encoding="utf-8"))
    only = set(sys.argv[1:])

    with sync_playwright() as pw:
        browser = pw.chromium.launch(channel="chrome", headless=True)
        page = browser.new_page(viewport={"width": 1400, "height": 1100})

        for i, item in enumerate(manifest, start=1):
            name = item["name"]
            out = RAY_DIR / f"{i:02d}-{name}.png"
            if only and name not in only:
                continue
            if out.exists() and out.stat().st_size > 5000:
                print("skip", out.name)
                continue

            code = (ROOT / item["file"]).read_text(encoding="utf-8").rstrip() + "\n"
            page.goto(URL, wait_until="networkidle", timeout=60000)
            page.wait_for_selector("textarea", timeout=30000)
            page.wait_for_timeout(1200)
            page.eval_on_selector("textarea", SET_VALUE, code)
            page.wait_for_timeout(1400)

            with page.expect_download(timeout=30000) as dl_info:
                page.click('button[aria-label="Export as PNG"]')
            dl = dl_info.value
            dl.save_as(out)
            print("saved", out.name, out.stat().st_size)

        browser.close()


if __name__ == "__main__":
    main()
