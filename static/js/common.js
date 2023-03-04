/*!
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

/**
 * Force target '_blank' with window.open JavaScript method.
 */
(function () {
    $('a').click(function () {
        var href = $(this).attr('href');

        if (
            href.indexOf('ph7builder.com') == -1 && href.indexOf('youtube.com') == -1 &&
            href.indexOf('youtu.be') == -1 && href.indexOf('vimeo.com') == -1 &&
            href.indexOf('dailymotion.com') == -1 && href.indexOf('metacafe.com') == -1 &&
            href.indexOf('gravatar.com') == -1 && href.indexOf('softaculous.com') == -1 &&
            (href.indexOf('http://') != -1 || href.indexOf('https://') != -1)

        ) {
            var host = href.substr(href.indexOf(':') + 3);
            if (host.indexOf('/') != -1) {
                host = host.substring(0, host.indexOf('/'));
            }
            if (host != window.location.host) {
                window.open(href);
                return false;
            }
        }
    })
})();

console.log('This Web App has been made with http://pH7Builder.com | The Social App Builder'
    + "\r\n" + 'GitHub: http://github.com/pH7Software/pH7-Social-Dating-CMS');
