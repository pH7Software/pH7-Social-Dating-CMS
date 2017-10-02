/*
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

var $validationBox = (function () {
    $.get(pH7Url.base + 'ph7cms-donation/main/donationbox', function (oData) {
        $.colorbox({
            width: '100%',
            width: '200px',
            height: '155px',
            speed: 500,
            scrolling: false,
            html: $(oData).find('#box_block')
        })
    })
});

$validationBox();
