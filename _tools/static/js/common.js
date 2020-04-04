/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

/**
 * Allows you to include a JavaScript file in an HTML page.
 *
 *
 * @example
 *     // With some files
 *     var aFiles['foo.js', 'bar.js', 'foobar.js'];
 *     pH7Include(aFiles);
 *
 *
 * @example
 *     // With a file
 *     pH7Include('foobar.js');
 *
 *
 * @param mixed ({String} | {Array}) JS file(s).
 * @return {Void}
 */
function pH7Include(mFile) {
    // Verify that the method isArray is available in the JavaScript version of the web browser (e.g., IE8).
    if (!Array.isArray) {
        Array.isArray = function (mArg) {
            return Object.prototype.toString.call(mArg) === '[object Array]';
        };
    }


    if (Array.isArray(mFile)) {
        for (iF in mFile) pH7Include(mFile[iF]);
    }
    else {
        var sHead = document.getElementsByTagName('head')[0];
        var sFoorer = document.getElementsByTagName('footer')[0]; // Only with HTML 5, more this tag must be present in the HTML document, but allows faster loading of the page because the files are loaded last.
        var mContainer = (sFoorer ? sFoorer : (sHead ? sHead : false));

        if (mContainer) {
            var oScript = document.createElement('script');
            oScript.src = mFile;
            /*
             // With HTML5 this is no longer necessary.
             oScript.type = 'text/javascript';
             */

            mContainer.appendChild(oScript);
        }
        else {
            alert('"pH7Include()" function must be included in a valid HTML code.');
        }
    }
}

/**
 * For target_blank with window.open JavaScript method.
 */
(function () {
    $('a').click(function () {
        var href = $(this).attr('href');

        if (
            href.indexOf('ph7cms.com') == -1 && href.indexOf('youtube.com') == -1 &&
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

console.log('This Web App has been made with http://pH7CMS.com | The Social App Builder'
    + "\r\n" + 'GitHub: http://github.com/pH7Software/pH7-Social-Dating-CMS');
