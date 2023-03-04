/*!
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

const disagreedRedirectLink = "https://google.com"; // <= URL to show when the visitor is likely to be under 18yrs

$(document).ready(function () {
    const oDisclaimer = new Disclaimer();

    if (!oDisclaimer.isAccepted()) {
        oDisclaimer.loadDialog();
        oDisclaimer.centerDialog();
    }

    document.getElementById('agree-over18').onclick = function () {
        oDisclaimer.disableDialog();
        oDisclaimer.setAccepted();
    };
    document.getElementById('disagree-under18').onclick = function () {
        location.href = disagreedRedirectLink
    }
});
