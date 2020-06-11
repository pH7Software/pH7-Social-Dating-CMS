/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

const localKeyName = 'agreed18';

class Disclaimer {
    constructor() {
        this.backgroundElement = $('#disclaimer-background');
        this.dialogElement = $('#disclaimer-dialog');

        this.dialogStatus = 0;
    }

    loadDialog() {
        if (this.dialogStatus == 0) {
            this.backgroundElement.css({
                'opacity': 0.95
            });
            this.backgroundElement.fadeIn('slow');
            this.dialogElement.fadeIn('slow');
            this.dialogStatus = 1;
        }
    }

    disableDialog() {
        if (this.dialogStatus == 1) {
            this.dialogElement.fadeOut('slow');
            this.backgroundElement.fadeOut('slow');
            this.dialogStatus = 0;
        }
    }

    centerDialog() {
        const windowHeight = document.documentElement.clientHeight;
        const windowWidth = document.documentElement.clientWidth;

        const dialogHeight = this.dialogElement.height();
        const dialogWidth = this.dialogElement.width();

        this.dialogElement.css({
            "position": "absolute",
            "top": windowHeight / 2 - dialogHeight / 2,
            "left": windowWidth / 2 - dialogWidth / 2
        });

        this.backgroundElement.css({
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
