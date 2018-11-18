var CACHE_NAME = 'homepage';

const CACHED_FILES = [
    '/',
    // TODO: Add more URLs to be cached here
];
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                console.info('[sw.js] cached the files.');
                return cache.addAll(CACHED_FILES);
            })
    );
});
