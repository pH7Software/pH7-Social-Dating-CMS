/*
 @see /templates/system/modules/pwa/themes/base/js/sw-register.js

 TODO: Implement the Service Worker to cache some static data, html pages, images, etc.
 More info: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API/Using_Service_Workers

 🚀 Feel free to fork the project: https://github.com/pH7Software/pH7-Social-Dating-CMS
 Commit your changes, then open a Pull Request for submitting your awesome changes 🎉
 */

const STATIC_CACHE_NAME = 'sta_homepage';
const DYNAMIC_CACHE_NAME = 'dyn_homepage';

const CACHED_FILES = [
    '/',
    // TODO: Add more URLs to be cached here
];

self.addEventListener('install', async event => {
    const cache = await caches.open(STATIC_CACHE_NAME);
    await cache.addAll(CACHED_FILES);
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (url.origin === location.origin) {
        event.respondWith(cachedData(request));
    } else {
        event.respondWith(fetchNetworkRequest(request));
    }
});

const isPageValid = (response) => {
    return response.ok && response.status === 200;
};

const cachedData = async (request) => {
    const cachedResponse = await caches.match(request);
    return cachedResponse || fetch(request);
};

const fetchNetworkRequest = async (request) => {
    const cache = await caches.open(DYNAMIC_CACHE_NAME);

    try {
        const response = await fetch(request);

        // Then, cache the page if 200 code (we don't want to cache a 404, 403, 500, ...)
        if(isPageValid(response)) {
            await cache.put(request, response.clone());
        }

        return response;
    } catch (error) {
        return await cache.match(request);
    }
};

// Add to Home Screen
self.addEventListener('fetch', (event) => {
    // Workaround for https://bugs.chromium.org/p/chromium/issues/detail?id=823392
    if (event.request.cache === 'only-if-cached' && event.request.mode !== 'same-origin') {
        return;
    }
    event.respondWith(
        //  Try to find response in cache...
        caches.match(event.request)
            .then((resp) => {
                // If response is found in cache, then return it, otherwise fetch it
                return resp || fetch(event.request);
            })
            .catch((error) => {
                // Something went wrong
                console.error("Worker couldn't fetch the request: ", error);
            })
    );
});
