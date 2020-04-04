/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// Only for visitors
$(document).ready(function () {
    var $oDialog = $('div#box').html('<iframe style="border:0px" src="' + pH7Url.base + 'user/asset/ajax/popup/signup" width="100%" height="100%"></iframe>').dialog
    ({
        open: function () {
            $('.ui-dialog-titlebar-close').hide()
        },
        autoOpen: false,
        closeOnEscape: false,
        modal: true,
        height: 600,
        width: 380,
        resizable: false,
        show: 'slide',
        hide: 'puff',
        title: pH7LangCore.join_now
    });

    setTimeout(function () {
        $('.ui-front').css('z-index', 10999); // z-index!!! Otherwise, the menu of the site will be in front.
        $('title').text(pH7LangCore.join_now); // Set an attractive title
        $oDialog.dialog('open');
    }, 5E3);
});
