/*!
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2026, Pierre-Henry Soria and pH7Builder contributors.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

/**
 * Open external links in a new tab.
 * Delegated from the document so links added later (e.g. by ajax) are covered too.
 */
(function () {
    var aWhitelistedHosts = [
        'ph7builder.com', 'youtube.com', 'youtu.be', 'vimeo.com',
        'dailymotion.com', 'gravatar.com', 'softaculous.com'
    ];

    $(document).on('click', 'a', function () {
        var sHref = $(this).attr('href');

        // Anchors without an href (buttons, JS hooks) have no URL to open
        if (!sHref || (sHref.indexOf('http://') === -1 && sHref.indexOf('https://') === -1)) {
            return;
        }

        for (var i = 0; i < aWhitelistedHosts.length; i++) {
            if (sHref.indexOf(aWhitelistedHosts[i]) !== -1) {
                return;
            }
        }

        var sHost = sHref.substr(sHref.indexOf(':') + 3);
        if (sHost.indexOf('/') !== -1) {
            sHost = sHost.substring(0, sHost.indexOf('/'));
        }

        if (sHost !== window.location.host) {
            // "noopener" prevents the opened page from hijacking this window via window.opener (reverse tabnabbing)
            window.open(sHref, '_blank', 'noopener');
            return false;
        }
    });
})();

console.info('pH7Builder — https://ph7builder.com'
    + "\r\n" + 'Source: https://github.com/pH7Software/pH7-Social-Dating-CMS');
