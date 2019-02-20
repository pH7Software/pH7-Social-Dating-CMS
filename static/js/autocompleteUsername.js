/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

$(document).ready(function () {
    $('input#recipient').autocomplete({
        source: function (request, callback) {
            var dataString = {username: request.term};
            $.ajax({
                type: 'POST',
                url: pH7Url.base + 'asset/ajax/autocompleteUsername',
                data: dataString,
                //cache: false,
                complete: function (oXhr, sResult) {
                    if (sResult != 'success') return;
                    var response = oXhr.responseText;
                    var usernames = [];
                    $(response).find('li username').each(function () {
                        usernames.push($(this).text());
                    });
                    callback(usernames);

                    var oUl = $('input#recipient').autocomplete('widget');
                    $(response).find('li avatar').each(function (sIndex) {
                        var img = $(this).text();
                        $(oUl).find('li:eq(' + sIndex + ') a')
                            .wrapInner('<span style="position:relative;top:-7px;left:10px"></span>')
                            .prepend('<img src="' + img + '" class="avatar" alt="Avatar" />');

                    });
                }
            });
        },
        open: function () {
            var oUl = $(this).autocomplete('widget');
            oUl.css('width', '400px');
        }
    })
});
