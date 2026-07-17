/*
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('[$url_root]service-worker.js', {
        scope: '[$url_relative]'
    }).then(function (registration) {
        console.log('Service Worker registered on scope: ' + registration.scope);
    }).catch(function (error) {
        console.log('Service Worker registration has failed: ', error);
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
    const url = new URL(scope);
    return url.pathname === '[$url_relative]';
}

/*
 * Install-prompt plumbing: the browser's install prompt is captured so themes can
 * offer an "Add to Home Screen" button wherever it fits their design.
 * Usage from any theme/module script:
 *   document.addEventListener('ph7:pwa-installable', function () { ... show your button ... });
 *   pH7Pwa.promptInstall(); // call from the button's click handler
 */
window.pH7Pwa = {
    deferredPrompt: null,

    isInstallable: function () {
        return this.deferredPrompt !== null;
    },

    promptInstall: function () {
        if (!this.deferredPrompt) {
            return Promise.resolve(null);
        }

        const promptEvent = this.deferredPrompt;
        this.deferredPrompt = null;

        promptEvent.prompt();
        return promptEvent.userChoice;
    }
};

window.addEventListener('beforeinstallprompt', function (event) {
    // Chrome shows a mini-infobar otherwise; defer so the site controls the moment
    event.preventDefault();
    window.pH7Pwa.deferredPrompt = event;
    document.dispatchEvent(new CustomEvent('ph7:pwa-installable'));
});

window.addEventListener('appinstalled', function () {
    window.pH7Pwa.deferredPrompt = null;
    document.dispatchEvent(new CustomEvent('ph7:pwa-installed'));
});
