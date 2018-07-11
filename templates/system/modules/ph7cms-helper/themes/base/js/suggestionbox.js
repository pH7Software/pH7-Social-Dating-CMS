/*
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

var $suggestionBox = (function () {
    $.get(pH7Url.base + 'ph7cms-helper/main/suggestionbox', function (oData) {
        $.colorbox({
            width: '240px',
            height: '220px',
            speed: 500,
            scrolling: false,
            html: $(oData).find('#box_block')
        })
    })
});

var $amendBoxBgColor = (function () {
    var aHexBgdColors = [
        '#fff',
        '#eceff1',
        '#defec8',
        '#ffdcd8'

    ];

    $('#cboxContent').css(
        'background-color',
        aHexBgdColors[Math.floor((Math.random() * aHexBgdColors.length))] + ' !important'
    );
});

$(document).ready(function () {
    $suggestionBox();
    $amendBoxBgColor();
});
