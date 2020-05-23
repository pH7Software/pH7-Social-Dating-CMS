/*
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2015-2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

var $suggestionBox = (function () {
    $.get(pH7Url.base + 'ph7cms-helper/main/suggestionbox', function (oData) {
        $.colorbox({
            width: '240px',
            height: '240px',
            speed: 500,
            scrolling: false,
            html: $(oData).find('#box_block')
        })
    })
});

var $amendBoxBgColor = (function () {
    var aHexColors = [
        '#ffffff',
        '#eceff1',
        '#ffdcd8',
        '#d1dce5',
        '#f9fbe7',
        '#ffe0b2',
        '#ffecb3',
        '#fff9c4',
        '#ffccbc',
        '#e0f7fa',
        '#fce4ec',
        '#b2dfdb',
        '#b3dfae'
    ];

    var sRandomHexColor = aHexColors[Math.floor((Math.random() * aHexColors.length))];
    $('#cboxContent').css('background-color', sRandomHexColor + ' !important');
});

$(document).ready(function () {
    $suggestionBox();
    $amendBoxBgColor();
});
