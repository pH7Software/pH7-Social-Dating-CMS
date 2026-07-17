/*
 * @see /templates/system/modules/pwa/themes/base/js/sw-register.js
 */

const CACHE_VERSION = 'ph7-pwa-v3';
const STATIC_CACHE_NAME = CACHE_VERSION + '-static';
const IMAGE_CACHE_NAME = CACHE_VERSION + '-images';
const MAX_IMAGE_CACHE_ENTRIES = 120;
const OFFLINE_PAGE_URL = './pwa/main/offline';
/*
 * Every entry is cached best-effort: the manifest/browserconfig are PHP routes
 * (there is no physical /manifest.json on standard installs), and a single 404
 * must never abort the whole service-worker installation.
 */
const PRE_CACHED_FILES = [
    './favicon.ico',
    './pwa/main/manifest',
    './pwa/main/browserconfig',
    './manifest.json',
    './browserconfig.xml'
];
const CACHEABLE_DESTINATIONS = [
    'font',
    'image',
    'manifest',
    'script',
    'style'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME)
            .then((cache) => Promise.all(
                PRE_CACHED_FILES.concat([OFFLINE_PAGE_URL])
                    .map((sUrl) => cache.add(sUrl).catch(() => null))
            ))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        Promise.all([
            // Lets the network request start while the service worker boots => faster navigations
            self.registration.navigationPreload ? self.registration.navigationPreload.enable() : Promise.resolve(),
            caches.keys()
                .then((cacheNames) => Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName.indexOf('ph7-pwa-') === 0 &&
                            cacheName !== STATIC_CACHE_NAME && cacheName !== IMAGE_CACHE_NAME)
                        .map((cacheName) => caches.delete(cacheName))
                ))
        ]).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Workaround for https://bugs.chromium.org/p/chromium/issues/detail?id=823392
    if (request.cache === 'only-if-cached' && request.mode !== 'same-origin') {
        return;
    }

    // Page navigations: network-first with an offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(navigationNetworkFirst(event));
        return;
    }

    if (!isCacheableRequest(request)) {
        return;
    }

    event.respondWith(cacheFirst(request));
});

const isCacheableRequest = (request) => {
    const url = new URL(request.url);

    return request.method === 'GET' &&
        url.origin === location.origin &&
        CACHEABLE_DESTINATIONS.indexOf(request.destination) !== -1;
};

const isResponseValid = (response) => {
    return response && response.ok && response.status === 200;
};

const navigationNetworkFirst = async (event) => {
    try {
        // Use the preloaded response when navigation preload is supported
        const preloadedResponse = await event.preloadResponse;
        if (preloadedResponse) {
            return preloadedResponse;
        }

        return await fetch(event.request);
    } catch (error) {
        const cachedOfflinePage = await caches.match(OFFLINE_PAGE_URL);
        if (cachedOfflinePage) {
            return cachedOfflinePage;
        }

        return minimalOfflineResponse();
    }
};

/**
 * Last-resort fallback if the offline page could not be pre-cached.
 */
const minimalOfflineResponse = () => {
    return new Response(
        '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Offline</title>' +
        '<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#15151c;color:#ececf2;text-align:center}div{padding:2em}h1{font-size:1.6em}button{margin-top:1em;padding:.6em 1.6em;border:0;border-radius:999px;background:linear-gradient(135deg,#e8467c,#7c5cff);color:#fff;font-weight:600;cursor:pointer}</style></head>' +
        '<body><div><h1>You are offline</h1><p>Check your connection and try again.</p><button onclick="location.reload()">Retry</button></div></body></html>',
        { status: 503, headers: { 'Content-Type': 'text/html; charset=utf-8' } }
    );
};

const cacheFirst = async (request) => {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    const response = await fetch(request);
    if (isResponseValid(response)) {
        const sCacheName = request.destination === 'image' ? IMAGE_CACHE_NAME : STATIC_CACHE_NAME;
        const cache = await caches.open(sCacheName);
        await cache.put(request, response.clone());

        if (request.destination === 'image') {
            trimCache(IMAGE_CACHE_NAME, MAX_IMAGE_CACHE_ENTRIES);
        }
    }

    return response;
};

/**
 * Keeps the image cache from growing unbounded (dating sites are photo-heavy);
 * evicts the oldest entries first.
 */
const trimCache = async (cacheName, maxEntries) => {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > maxEntries) {
        await cache.delete(keys[0]);
        await trimCache(cacheName, maxEntries);
    }
};
