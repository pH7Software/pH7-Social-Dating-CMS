/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

$(document).ready(function () {
    var $oRecipient = $('input#recipient');
    if (!$oRecipient.length) {
        return;
    }

    var oAutocomplete = $oRecipient.autocomplete({
        position: {collision: 'flipfit'},
        source: function (oRequest, oResponse) {
            $.ajax({
                type: 'POST',
                url: pH7Url.base + 'asset/ajax/autocompleteUsername',
                data: {username: oRequest.term},
                dataType: 'html',
                success: function (sHtml) {
                    var aUsers = $($.parseHTML(sHtml)).find('li').map(function () {
                        var $oUser = $(this);
                        return {
                            label: $oUser.find('username').text(),
                            value: $oUser.find('username').text(),
                            avatar: $oUser.find('avatar').text()
                        };
                    }).get();
                    oResponse(aUsers);
                },
                error: function () {
                    oResponse([]);
                }
            });
        }
    }).autocomplete('instance');

    oAutocomplete._renderItem = function ($oList, oItem) {
        var $oContent = $('<div>').css({display: 'flex', gap: '8px', alignItems: 'center', overflowWrap: 'anywhere'});
        if (/^https?:\/\//i.test(oItem.avatar)) {
            $oContent.append($('<img>', {src: oItem.avatar, 'class': 'avatar', alt: '', width: 32, height: 32}));
        }
        $oContent.append($('<span>').text(oItem.label));
        return $('<li>').append($oContent).appendTo($oList);
    };

    oAutocomplete._resizeMenu = function () {
        this.menu.element.outerWidth(Math.min(Math.max(this.element.outerWidth(), 240), $(window).width() - 24));
    };
});
