if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('./sw.js', {
        scope: '/'
    }).then(function (reg) {
        console.log('Service Worker registered on scope:' + reg.scope);
    }).catch(function (err) {
        console.log('Service Worker registration has failed: ', err);
    });

    navigator.serviceWorker.getRegistrations().then(function (registrations) {
        registrations.forEach(function (registration) {
            if (!isBaseUrl(registration.scope)) {
                return registration.unregister();
            }
        });
    });
}

function isBaseUrl(scope) {
    var url = new URL(scope);
    return url.pathname === '/';
}
