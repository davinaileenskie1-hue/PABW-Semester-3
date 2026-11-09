// Screenshot helper for the lab report using Chrome DevTools Protocol.
// Usage: node shot.js
const fs = require('node:fs');
const path = require('node:path');

const OUT = path.join(__dirname, '..', 'laporan-assets', 'dokumentasi');
const BASE = 'http://127.0.0.1:8123';
const CDP = 'http://127.0.0.1:9222';

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function newTab(url) {
    const res = await fetch(`${CDP}/json/new?${encodeURIComponent('about:blank')}`, { method: 'PUT' });
    const tab = await res.json();
    return tab;
}

async function connect(wsUrl) {
    const ws = new WebSocket(wsUrl);
    await new Promise((resolve, reject) => {
        ws.onopen = resolve;
        ws.onerror = reject;
    });

    let id = 0;
    const pending = new Map();
    ws.onmessage = (event) => {
        const msg = JSON.parse(event.data);
        if (msg.id && pending.has(msg.id)) {
            const { resolve, reject } = pending.get(msg.id);
            pending.delete(msg.id);
            msg.error ? reject(new Error(JSON.stringify(msg.error))) : resolve(msg.result);
        }
    };

    const send = (method, params = {}) => new Promise((resolve, reject) => {
        const msgId = ++id;
        pending.set(msgId, { resolve, reject });
        ws.send(JSON.stringify({ id: msgId, method, params }));
    });

    return { send, close: () => ws.close() };
}

async function main() {
    const tab = await newTab();
    const client = await connect(tab.webSocketDebuggerUrl);
    const { send } = client;

    await send('Page.enable');
    await send('Runtime.enable');
    await send('Emulation.setDeviceMetricsOverride', {
        width: 1440, height: 1000, deviceScaleFactor: 1, mobile: false,
    });

    const goto = async (url) => {
        await send('Page.navigate', { url });
        await sleep(2500);
    };

    const evaluate = async (expression) => {
        const r = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
        return r.result?.value;
    };

    const shot = async (name, fullPage = false) => {
        let clip;
        if (fullPage) {
            const size = await evaluate('JSON.stringify({w: document.documentElement.scrollWidth, h: Math.min(document.documentElement.scrollHeight, 2400)})');
            const { w, h } = JSON.parse(size);
            clip = { x: 0, y: 0, width: w, height: h, scale: 1 };
        }
        const { data } = await send('Page.captureScreenshot', {
            format: 'png',
            clip,
            captureBeyondViewport: fullPage,
        });
        fs.writeFileSync(path.join(OUT, name + '.png'), Buffer.from(data, 'base64'));
        console.log('saved', name);
    };

    const login = async (email) => {
        await goto(`${BASE}/logout`).catch(() => {});
        await goto(`${BASE}/login`);
        const ok = await evaluate(`(async () => {
            const token = await fetch('/sanctum/csrf-cookie').catch(() => null);
            const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            const body = new FormData();
            body.append('email', ${JSON.stringify(email)});
            body.append('password', 'password');
            const r = await fetch('/login', {
                method: 'POST',
                body,
                headers: { 'X-XSRF-TOKEN': decodeURIComponent(m[1]) },
                redirect: 'manual',
            });
            return r.status;
        })()`);
        console.log('login', email, 'status', ok);
        await goto(`${BASE}/dashboard`);
    };

    await goto(`${BASE}/`);
    await sleep(1000);
    await shot('01-beranda');

    await goto(`${BASE}/login`);
    await shot('02-login');

    await login('admin@example.com');
    await shot('03-dashboard-admin');

    await goto(`${BASE}/pendaftaran`);
    await sleep(1500);
    await shot('04-pendaftaran');

    await goto(`${BASE}/admin/users`);
    await sleep(1500);
    await shot('05-kelola-user-admin');

    await goto(`${BASE}/admin/users/create`);
    await shot('06-tambah-user-baru');

    // Edit page for the regular user (id 2)
    await goto(`${BASE}/admin/users/2/edit`);
    await shot('07-edit-user');

    // Validation errors on the create form
    await goto(`${BASE}/admin/users/create`);
    await sleep(1000);
    await evaluate(`(async () => {
        const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        const body = new FormData();
        body.append('name', '');
        body.append('email', 'bukan-email');
        body.append('role', 'superadmin');
        body.append('password', 'x');
        body.append('password_confirmation', 'y');
        const r = await fetch('/admin/users', {
            method: 'POST',
            body,
            headers: { 'X-XSRF-TOKEN': decodeURIComponent(m[1]) },
            redirect: 'manual',
        });
        return r.status;
    })()`);
    await goto(`${BASE}/admin/users/create`);
    await sleep(1500);
    await shot('08-validasi-user');

    await login('user@example.com');
    await shot('09-dashboard-user');

    await goto(`${BASE}/admin/users`);
    await shot('10-user-403');

    client.close();
}

main().catch((error) => {
    console.error(error);
    process.exit(1);
});
