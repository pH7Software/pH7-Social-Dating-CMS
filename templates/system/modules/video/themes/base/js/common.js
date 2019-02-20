/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

$('#video-type').on('change', function () {
    var sShowCss = 'display:block !important;visibility:visible !important';
    var sHideCss = 'display:none !important;visibility:none !important';
    var sVal = $(this).val();

    switch (sVal) {
        case 'regular': {
            $('#regular').fadeIn().attr('style', sShowCss);
            $('#embed').attr('style', sHideCss);
            disable_button(); // This function is in the file: ~/static/js/form.js
        }
            break;

        case 'embed': {
            $('#embed').fadeIn().attr('style', sShowCss);
            $('#regular').attr('style', sHideCss);
            enable_button(); // This function is located in the file: ~/static/js/form.js
        }
            break;

        default: {
            $('#regular').attr('style', sHideCss);
            $('#embed').attr('style', sHideCss);
        }
    }
});
