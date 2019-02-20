/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// This feature is only for members!
function mail(sType, iMsgId, sCSRFToken) {
    $.post(pH7Url.base + 'mail/asset/ajax/Mail', {
        type: sType,
        msg_id: iMsgId,
        security_token: sCSRFToken
    }, function (oResponseData) {
        if (oResponseData.status == 1) {
            $('.msg').addClass('alert alert-success');
            $('#mail_' + iMsgId).hide('slow');
        } else {
            $('.msg').addClass('alert alert-danger');
        }

        $('.msg').text(oResponseData.txt).fadeOut(2000);

        window.location.reload(); // To generate a new token valid.
    }, 'json');
}
