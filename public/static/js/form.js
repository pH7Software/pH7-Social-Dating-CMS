/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

var sButtonPattern = 'button[type=submit]';

function enable_button()
{
    $(sButtonPattern).attr('disabled', false);
    $(sButtonPattern).css({background:'#E6E6E6'});
}

function disable_button()
{
    $(sButtonPattern).attr('disabled', 'disabled');
    $(sButtonPattern).css({background:'#FFF'});
}

var sInputAgree = 'input[name="agree[]"]';
$(sInputAgree).click(function()
{
    $(sInputAgree+':checked').val()==1?enable_button():disable_button();
});

$('input[name=all_action]').on('click', function() {
    $('input[name="action[]"]').prop('checked', $(this).is(':checked'));
});
