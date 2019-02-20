/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function report(sType, iReportId, sCSRFToken) {
    $.post(pH7Url.base + 'report/asset/ajax/Report', {
        type: sType,
        reportId: iReportId,
        security_token: sCSRFToken
    }, function (oResponseData) {
        if (oResponseData.status == 1) {
            $('.msg').addClass('alert alert-success');
            $('#report_' + iReportId).hide('slow');
        } else {
            $('.msg').addClass('alert alert-danger');
        }

        $('.msg').text(oResponseData.txt).fadeOut(2000);

        window.location.reload(); // To generate a new token valid. This is now mandatory as ajax file automatically generates a token while the other page does not regenerate.
    }, 'json');
}
