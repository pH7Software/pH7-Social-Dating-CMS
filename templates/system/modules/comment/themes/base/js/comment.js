/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// This feature is only for members!
function comment(sType, iCommentId, iRecipientId, iSenderId, sTable, sCSRFToken) {
    var oDataString = {
        type: sType,
        id: iCommentId,
        recipient_id: iRecipientId,
        sender_id: iSenderId,
        table: sTable,
        security_token: sCSRFToken
    };

    $.post(pH7Url.base + 'comment/asset/ajax/Comment', oDataString, function (oResponseData) {
        if (oResponseData.status == 1) {
            $('.msg').show();
            $('.msg').addClass('alert alert-success');
            $('#' + iCommentId).hide('slow');
        } else {
            $('.msg').addClass('alert alert-danger');
        }

        $('.msg').text(oResponseData.txt).fadeOut(2000);

        window.location.reload(); // To generate a new token valid.
    }, 'json');
}
