const VERSION = 'asee-technician-v1-' + self.ASEE_BUILD;
const assets = new URL('../../assets/maintenance/', self.location.href);
const scope = new URL('./', self.location.href);
const staticFiles = ['technician.js', 'storage.js', 'technician.css', 'reference/checklists.js', 'reference/company.js', 'reference/machine-types.js', 'reference/chiller-fields.js'];

self.addEventListener('install', event => {
    event.waitUntil((async () => {
        await (await caches.open(VERSION)).addAll(staticFiles.map(file => new URL(file, assets).href));
        const shell = await fetch(new URL('create', scope), {cache: 'no-store'});
        if (!shell.ok || !shell.headers.get('X-Maintenance-User')) throw new Error('Offline shell was not available');
        await saveShell(shell);
    })());
    // An existing form keeps its current worker until it is closed/reloaded.
});
self.addEventListener('activate', event => {
    event.waitUntil((async () => {
        for (const key of await caches.keys()) {
            if (key.startsWith('asee-technician-v') && key !== VERSION && !key.startsWith(VERSION + '-')) await caches.delete(key);
        }
        await self.clients.claim();
    })());
});
self.addEventListener('message', event => {
    if (event.data?.type === 'LOGOUT') event.waitUntil((async () => {
        for (const key of await caches.keys()) if (key.startsWith(VERSION + '-shell')) await caches.delete(key);
        await caches.delete(VERSION + '-active');
    })());
    if (event.data?.type === 'PREPARE') event.waitUntil((async () => {
        const response = await fetch(new URL('create', scope), {cache: 'no-store'});
        await saveShell(response);
    })());
});

async function saveShell(response) {
    const user = response.headers.get('X-Maintenance-User');
    if (!response.ok || response.redirected || !user) return;
    await (await caches.open(VERSION + '-shell-' + user)).put(new URL('create', scope), response.clone());
    await (await caches.open(VERSION + '-active')).put(new URL('active', scope), new Response(user));
}

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== scope.origin) return;
    if (staticFiles.some(file => new URL(file, assets).href === url.href)) {
        event.respondWith(caches.open(VERSION).then(async cache => (await cache.match(event.request)) || fetch(event.request)));
        return;
    }
    // Never cache JSON, document downloads, PDFs, login pages or authenticated mutations.
    if (event.request.mode === 'navigate' && url.pathname.startsWith(scope.pathname)
        && /\/(create|\d+)$/.test(url.pathname)) {
        event.respondWith((async () => {
            try {
                const response = await fetch(event.request);
                await saveShell(response);
                if (response.redirected || response.status === 401 || response.status === 403) {
                    await caches.delete(VERSION + '-active');
                }
                return response;
            } catch (error) {
                const active = await (await caches.open(VERSION + '-active')).match(new URL('active', scope));
                if (active) {
                    const cached = await (await caches.open(VERSION + '-shell-' + await active.text())).match(new URL('create', scope));
                    if (cached) return cached;
                }
                return new Response('افتح واجهة الفني مرة أثناء الاتصال لتحضيرها للعمل بدون إنترنت. Open the technician portal online once to prepare offline access.', {status: 503, headers: {'Content-Type': 'text/plain; charset=utf-8'}});
            }
        })());
    }
});
