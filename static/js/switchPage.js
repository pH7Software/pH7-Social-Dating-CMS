/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

/**
 * Switch Page.
 *
 * @param {String} The ID name of link.
 * @return {Void}
 */
function switchPage(sLinkId) {
    $("#switchPage").children().removeClass().toggleClass("hidden");
    $("#switchPage " + sLinkId).removeClass().toggleClass("visible");
}

$(document).ready(function () {
    $('#switchPage a').click(function () {
        $(this).attr('href', switchPage($(this).attr('href')));
    });
});
