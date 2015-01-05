/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

$('select[name=type]').click(function()
{
    var sVal = $(this).val();

    if(sVal == 'regular') {
        $('#regular').slideDown('fast');
        $('#embed').hide();
        disable_button(); // This function is in the file: ~/static/js/form.js
    }

    if(sVal == 'embed') {
        $('#embed').slideDown('fast');
        $('#regular').hide();
        enable_button(); // This function is in the file: ~/static/js/form.js
    }
});
