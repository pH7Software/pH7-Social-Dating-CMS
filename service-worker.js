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
    cache.addAll(CACHED_FILES);
});

self.addEventListener('fetch', event => {
    const {request} = event;
    const url = new URL(request.url);

    if (url.origin === location.origin) {
        event.respondWith(cacheData(request));
    } else {
        event.respondWith(networkFirst(request));
    }
});

async function cacheData(request) {
    const cachedResponse = await caches.match(request);
    return cachedResponse || fetch(request);
}

async function networkFirst(request) {
    const cache = await caches.open(DYNAMIC_CACHE_NAME);

    try {
        const response = await fetch(request);
        cache.put(request, response.clone());
        return response;
    } catch (error) {
        return await cache.match(request);
    }
}
