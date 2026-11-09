// Exports code snippets as images from ray.so through Chrome DevTools Protocol.
// Usage: node laporan-tools/ray.cjs
//
// The code is typed into the ray.so editor, the settings panel and header are
// hidden, and the code frame element itself is screenshotted with a white page
// background (background=false makes the card corners transparent).
const fs = require('node:fs');
const path = require('node:path');

const ROOT = path.join(__dirname, '..');
const OUT = path.join(ROOT, 'laporan-assets', 'ray');
const CDP = 'http://127.0.0.1:9222';
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const snippets = [
    ['01-UserRole.php', 'php', 'app/Enums/UserRole.php'],
    ['02-migration-role.php', 'php', 'database/migrations/2026_11_09_014624_add_role_to_users_table.php'],
    ['03-User.php', 'php', 'app/Models/User.php'],
    ['04-EnsureUserHasRole.php', 'php', 'app/Http/Middleware/EnsureUserHasRole.php'],
    ['05-bootstrap-app.php', 'php', 'bootstrap/app.php'],
    ['06-modules-statuses.json', 'json', 'modules_statuses.json'],
    ['07-UserManagement-routes.php', 'php', 'Modules/UserManagement/routes/web.php'],
    ['08-UserManagementController.php', 'php', 'Modules/UserManagement/app/Http/Controllers/UserManagementController.php'],
    ['09-StoreUserRequest.php', 'php', 'Modules/UserManagement/app/Http/Requests/StoreUserRequest.php'],
    ['10-UpdateUserRequest.php', 'php', 'Modules/UserManagement/app/Http/Requests/UpdateUserRequest.php'],
    ['11-user-form.tsx', 'tsx', 'Modules/UserManagement/resources/js/components/user-form.tsx'],
    ['12-UserManagement-index.tsx', 'tsx', 'Modules/UserManagement/resources/js/pages/index.tsx'],
    ['13-UserManagement-create.tsx', 'tsx', 'Modules/UserManagement/resources/js/pages/create.tsx'],
    ['14-Pendaftaran-routes.php', 'php', 'Modules/Pendaftaran/routes/web.php'],
    ['15-PendaftaranController.php', 'php', 'Modules/Pendaftaran/app/Http/Controllers/PendaftaranController.php'],
    ['16-DaftarRequest.php', 'php', 'Modules/Pendaftaran/app/Http/Requests/DaftarRequest.php'],
    ['17-Daftar.php', 'php', 'Modules/Pendaftaran/app/Models/Daftar.php'],
    ['18-http.ts', 'tsx', 'resources/js/lib/http.ts'],
    ['19-Pendaftaran-index.tsx', 'tsx', 'Modules/Pendaftaran/resources/js/pages/index.tsx'],
    ['20-alert-message.tsx', 'tsx', 'resources/js/components/alert-message.tsx'],
    ['21-app-sidebar.tsx', 'tsx', 'resources/js/components/app-sidebar.tsx'],
    ['22-app.tsx', 'tsx', 'resources/js/app.tsx'],
    ['23-HandleInertiaRequests.php', 'php', 'app/Http/Middleware/HandleInertiaRequests.php'],
    ['24-use-shared-props.ts', 'tsx', 'resources/js/hooks/use-shared-props.ts'],
    ['25-DashboardController.php', 'php', 'app/Http/Controllers/DashboardController.php'],
    ['26-DatabaseSeeder.php', 'php', 'database/seeders/DatabaseSeeder.php'],
];

async function connect(wsUrl) {
    const ws = new WebSocket(wsUrl);
    await new Promise((res, rej) => { ws.onopen = res; ws.onerror = rej; });
    let id = 0;
    const pending = new Map();
    ws.onmessage = (e) => {
        const m = JSON.parse(e.data);
        if (m.id && pending.has(m.id)) {
            const { resolve, reject } = pending.get(m.id);
            pending.delete(m.id);
            m.error ? reject(new Error(JSON.stringify(m.error))) : resolve(m.result);
        }
    };
    const send = (method, params = {}) => new Promise((resolve, reject) => {
        const i = ++id; pending.set(i, { resolve, reject });
        ws.send(JSON.stringify({ id: i, method, params }));
    });
    return { send, close: () => ws.close() };
}

(async () => {
    fs.mkdirSync(OUT, { recursive: true });
    const tab = await (await fetch(`${CDP}/json/new?about:blank`, { method: 'PUT' })).json();
    const { send, close } = await connect(tab.webSocketDebuggerUrl);
    await send('Page.enable');
    await send('Runtime.enable');
    await send('DOM.enable');
    await send('Emulation.setDeviceMetricsOverride', { width: 1500, height: 1200, deviceScaleFactor: 1, mobile: false });

    const ev = async (expression) => {
        const r = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
        if (r.exceptionDetails) {
            throw new Error(r.exceptionDetails.text + ' ' + (r.exceptionDetails.exception?.description ?? ''));
        }
        return r.result?.value;
    };

    for (const [name, language, file] of snippets) {
        const target = path.join(OUT, name.replace(/\.(php|json|tsx|ts)$/, '') + '.png');
        if (fs.existsSync(target) && fs.statSync(target).size > 10000) {
            console.log('skip', path.basename(target));
            continue;
        }
        const code = fs.readFileSync(path.join(ROOT, file), 'utf8').replace(/\r\n/g, '\n').replace(/\s+$/, '');
        const url = `https://ray.so/#background=false&padding=16&lineNumbers=true&language=${language}`;
        await send('Page.navigate', { url });

        let ready = false;
        for (let i = 0; i < 60; i++) {
            await sleep(300);
            ready = await ev("!!document.querySelector('textarea')");
            if (ready) break;
        }
        if (!ready) throw new Error('ray.so editor not ready for ' + name);

        await ev(`(() => {
            const el = document.querySelector('textarea');
            const setter = Object.getOwnPropertyDescriptor(window.HTMLTextAreaElement.prototype, 'value').set;
            setter.call(el, ${JSON.stringify(code)});
            el.dispatchEvent(new Event('input', { bubbles: true }));
            // Move the caret out of the way so the cursor does not blink in the shot.
            el.blur();
        })()`);
        await sleep(1400);

        const rect = await ev(`(() => {
            const style = document.createElement('style');
            style.textContent = \`html,body{background:#ffffff !important}
                [class*="transparentPattern"] { display:none !important; }
                [class*="code-module"][class*="app"] { background-image:none !important; background-color:#ffffff !important; }\`;
            document.head.appendChild(style);

            // Hide the floating settings panel (the element that holds the
            // theme/background/line numbers switches).
            const switches = [...document.querySelectorAll('[role="switch"]')];
            if (switches.length) {
                let panel = switches[0].parentElement;
                for (let k = 0; k < 6 && panel; k++) {
                    if (panel.querySelectorAll('[role="switch"]').length >= 2) break;
                    panel = panel.parentElement;
                }
                if (panel) panel.style.display = 'none';
            }

            // Hide the top navigation bar.
            const main = document.querySelector('main');
            const nav = main && [...main.children].find((el) => el.getBoundingClientRect().height < 90 && el.querySelector('button'));
            if (nav) nav.style.display = 'none';

            const frame = document.querySelector('[class*="Frame-module"][class*="resizableFrame"]')
                || document.querySelector('[class*="resizableFrame"]')
                || document.querySelector('textarea').closest('[class*="outerFrame"]');
            if (!frame) return null;
            const r = frame.getBoundingClientRect();
            return { x: Math.round(r.x) + window.scrollX, y: Math.round(r.y) + window.scrollY, width: Math.ceil(r.width), height: Math.ceil(r.height) };
        })()`);
        if (!rect) throw new Error('code frame not found for ' + name);

        await sleep(250);
        const { data } = await send('Page.captureScreenshot', {
            format: 'png',
            clip: { ...rect, scale: 2 },
            captureBeyondViewport: true,
        });
        fs.writeFileSync(target, Buffer.from(data, 'base64'));
        console.log('saved', path.basename(target), Math.round(fs.statSync(target).size / 1024) + 'KB', `${rect.width}x${rect.height}`);
    }

    close();
})();
