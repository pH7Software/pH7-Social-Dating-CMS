/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

const localKeyName = 'agreed18';

class Disclaimer {
    constructor() {
        this.dialogStatus = 0;
    }

    loadDialog() {
        if (this.dialogStatus == 0) {
            $('#disclaimer-background').css({
                'opacity': 0.95
            });
            $('#disclaimer-background').fadeIn('slow');
            $('#disclaimer-window').fadeIn('slow');
            this.dialogStatus = 1;
        }
    }

    disableDialog() {
        if (this.dialogStatus == 1) {
            $('#disclaimer-window').fadeOut('slow');
            $('#disclaimer-background').fadeOut('slow');
            this.dialogStatus = 0;
        }
    }

    centerDialog() {
        const windowWidth = document.documentElement.clientWidth;
        const windowHeight = document.documentElement.clientHeight;

        const dialogHeight = $('#disclaimer-window').height();
        const dialogWidth = $('#disclaimer-window').width();

        $('#disclaimer-window').css({
            "position": "absolute",
            "top": windowHeight / 2 - dialogHeight / 2,
            "left": windowWidth / 2 - dialogWidth / 2
        });

        $('#disclaimer-background').css({
            "height": windowHeight
        });
    }

    isAccepted() {
        try {
            return sessionStorage.getItem(localKeyName);
        } catch (e) {
            console.log('Cannot use sessionStorage', e);
        }

        return null;
    }

    setAccepted() {
        try {
            sessionStorage.setItem(localKeyName, '1');
        } catch (e) {
            console.log('Cannot use sessionStorage', e);
        }
    }
}
