/*
 * @see /templates/system/modules/pwa/themes/base/js/sw-register.js
 */

const CACHE_VERSION = 'ph7-pwa-v2';
const STATIC_CACHE_NAME = CACHE_VERSION + '-static';
const PRE_CACHED_FILES = [
    './favicon.ico',
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
            .then((cache) => cache.addAll(PRE_CACHED_FILES))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.indexOf('ph7-pwa-') === 0 && cacheName !== STATIC_CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Workaround for https://bugs.chromium.org/p/chromium/issues/detail?id=823392
    if (request.cache === 'only-if-cached' && request.mode !== 'same-origin') {
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

const cacheFirst = async (request) => {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    const response = await fetch(request);
    if (isResponseValid(response)) {
        const cache = await caches.open(STATIC_CACHE_NAME);
        await cache.put(request, response.clone());
    }

    return response;
};
