/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function cache(sType, sCSRFToken) {
    $.post(pH7Url.base + pH7Url.admin_mod + 'asset/ajax/Cache', {
        type: sType,
        security_token: sCSRFToken
    }, function (oResponseData) {
        (oResponseData.status == 1) ? $('.msg').addClass('alert alert-success') : $('.msg').addClass('alert alert-danger');
        $('.msg').text(oResponseData.txt).fadeOut(1000);
        window.location.reload();
    }, 'json');
}

function ads(sType, iAdId, sTable, sCSRFToken) {
    $.post(pH7Url.base + pH7Url.admin_mod + 'asset/ajax/Ads', {
        type: sType,
        adId: iAdId,
        table: sTable,
        security_token: sCSRFToken
    }, function (oResponseData) {
        if (oResponseData.status == 1) {
            $('.msg').addClass('alert alert-success');
            $('#ad_' + iAdId).hide('slow');
        }
        else {
            $('.msg').addClass('alert alert-danger');
        }

        $('.msg').text(oResponseData.txt).fadeOut(2000);

        window.location.reload(); // To generate a new token valid. This is now mandatory as ajax file automatically generates a token while the other page does not regenerate.
    }, 'json');
}
