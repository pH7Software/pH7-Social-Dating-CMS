/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

$('.hon_click').click(function () {
    window.location.reload(true);
});

$(document).on('keydown.ph7HotOrNot', function (oEvent) {
    var $oTarget = $(oEvent.target);
    var sActionId;

    if (
        oEvent.altKey || oEvent.ctrlKey || oEvent.metaKey || oEvent.shiftKey ||
        $oTarget.is('a, input, textarea, select, button') ||
        $oTarget.closest('[contenteditable]').length
    ) {
        return;
    }

    if (oEvent.key === 'ArrowLeft') {
        sActionId = 'hotornot_skip';
    } else if (oEvent.key === 'ArrowRight') {
        sActionId = 'hotornot_interested';
    } else {
        return;
    }

    var oAction = document.getElementById(sActionId);
    if (oAction) {
        oEvent.preventDefault();
        oAction.click();
    }
});
