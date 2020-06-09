/*!
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

const disagreedRedirectLink = "https://google.com";

$(document).ready(function () {
    let oDisclaimer = new Disclaimer();

    if (!oDisclaimer.isAccepted()) {
        oDisclaimer.centerDialog();
        oDisclaimer.loadDialog();
    }

    document.getElementById('agree-over18').onclick = function () {
        oDisclaimer.disableDialog();
        oDisclaimer.setAccepted();
    };
    document.getElementById('disagree-under18').onclick = function () {
        location.href = disagreedRedirectLink
    }
});
