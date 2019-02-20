/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

$('#post_id').keyup(function () {
    $('.your-address').hide();
    var iProfileId = $(this).data('profile_id');
    var iPostId = $('#post_id').val();

    $.ajax({
        type: 'POST',
        url: pH7Url.base + 'note/asset/ajax/form/checkPostId',
        data: {'profile_id': iProfileId, 'post_id': iPostId},
        success: function (oData) {
            var oResponseData = $.parseJSON(oData);

            if (oResponseData.status == 1) {
                $('.post_id').fadeIn();
                $('#post_id').css('border', 'solid #00cc00 1px');
                $('.post_id').css('color', "#149541");
            }
            else {
                $('.post_id').fadeIn();
                $('#post_id').css('border', 'solid #cc0000 1px');
                $('.post_id').css('color', '#F55');
            }

            $('.post_id').text(iPostId.substring(0, 60));
        }
    });
});
