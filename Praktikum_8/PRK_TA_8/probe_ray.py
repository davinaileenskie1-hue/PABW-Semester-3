from playwright.sync_api import sync_playwright

URL = "https://ray.so/#background=false&padding=16&lineNumbers=true&language=php"

with sync_playwright() as pw:
    browser = pw.chromium.launch(channel="chrome", headless=True)
    page = browser.new_page(viewport={"width": 1400, "height": 1000})
    page.goto(URL, wait_until="networkidle", timeout=60000)
    page.wait_for_timeout(2500)

    print("TITLE:", page.title())
    print("TEXTAREAS:", page.locator("textarea").count())
    print("BUTTONS:")
    for b in page.locator("button").all()[:40]:
        try:
            print("   label=", repr(b.get_attribute("aria-label")), "text=", repr(b.inner_text()[:40]))
        except Exception as e:
            print("   err", e)
    print("BODY TEXT SAMPLE:", page.inner_text("body")[:400].replace("\n", " | "))
    browser.close()
