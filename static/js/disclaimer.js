/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

const disagreedRedirectLink = "https://google.com";
const cookieName = 'agreed18';
let dialogStatus = 0;

$(document).ready(function () {
    if (!doesCookieExist(cookieName)) {
        centerPopup();
        loadDisclaimer();
    }

    $('#agree-over18').on('click', function () {
        disableDisclaimer();
        document.cookie = cookieName + '=true';
    });

    $('#disagree-under18').on('click', function () {
        location.href = disagreedRedirectLink
    });
});

function loadDisclaimer() {
    if (dialogStatus == 0) {
        $('#disclaimer-background').css({
            'opacity': 0.95
        });
        $('#disclaimer-background').fadeIn('slow');
        $('#disclaimer-window').fadeIn('slow');
        dialogStatus = 1;
    }
}

function disableDisclaimer() {
    if (dialogStatus == 1) {
        $('#disclaimer-window').fadeOut('slow');
        $('#disclaimer-background').fadeOut('slow');
        dialogStatus = 0;
    }
}

function centerPopup() {
    const windowWidth = document.documentElement.clientWidth;
    const windowHeight = document.documentElement.clientHeight;

    const popupHeight = $('#disclaimer-window').height();
    const popupWidth = $('#disclaimer-window').width();

    $('#disclaimer-window').css({
        "position": "absolute",
        "top": windowHeight / 2 - popupHeight / 2,
        "left": windowWidth / 2 - popupWidth / 2
    });

    $('#disclaimer-background').css({
        "height": windowHeight
    });
}

function doesCookieExist(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');

    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
