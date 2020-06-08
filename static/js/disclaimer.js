/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

const disagreedRedirectLink = "https://google.com";
const localKeyName = 'agreed18';

$(document).ready(function () {
    let oDisclaimer = new Disclaimer();

    if (!oDisclaimer.isAccepted()) {
        oDisclaimer.centerDialog();
        oDisclaimer.load();
    }

    $('#agree-over18').on('click', function () {
        oDisclaimer.disable();
        oDisclaimer.setAccepted();
    });

    $('#disagree-under18').on('click', function () {
        location.href = disagreedRedirectLink
    });
});

class Disclaimer {
    constructor() {
        this.dialogStatus = 0;
    }

    load() {
        if (this.dialogStatus == 0) {
            $('#disclaimer-background').css({
                'opacity': 0.95
            });
            $('#disclaimer-background').fadeIn('slow');
            $('#disclaimer-window').fadeIn('slow');
            this.dialogStatus = 1;
        }
    }

    disable() {
        if (this.dialogStatus == 1) {
            $('#disclaimer-window').fadeOut('slow');
            $('#disclaimer-background').fadeOut('slow');
            this.dialogStatus = 0;
        }
    }

    centerDialog() {
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

    isAccepted() {
        try {
            return localStorage.getItem(localKeyName);
        } catch (e) {
            console.log('Cannot use localStorage', e);
        }

        return null;
    }

    setAccepted() {
        try {
            localStorage.setItem(localKeyName, '1');
        } catch (e) {
            console.log('Cannot use localStorage', e);
        }
    }
}
