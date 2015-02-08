/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

$('select[name=type]').click(function()
{
    var sShowCss = 'display:block !important;visibility:visible !important';
    var sHideCss = 'display:none !important;visibility:none !important';
    var sVal = $(this).val();

    if(sVal == 'regular')
    {
        $('#regular').fadeIn(500).attr('style', sShowCss);
        $('#embed').attr('style', sHideCss);
        disable_button(); // This function is in the file: ~/static/js/form.js
    }

    if(sVal == 'embed')
    {
        $('#embed').fadeIn(500).attr('style', sShowCss);
        $('#regular').attr('style', sHideCss);
        enable_button(); // This function is located in the file: ~/static/js/form.js
    }
});
