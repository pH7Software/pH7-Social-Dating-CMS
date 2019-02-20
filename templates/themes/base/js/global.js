/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function showField(sName, mShow) {
    if ($('#' + sName).css("display") == "none" && (typeof mShow == 'undefined' || mShow == '') || mShow == 1) {
        $('#' + sName).fadeIn("fast");
    } else {
        $('#' + sName).fadeOut("fast");
    }
}

const $goBox = (function () {
    // Video Popup (video tag)
    $("a[data-popup=video]").colorbox({
        speed: 500,
        scrolling: false,
        inline: true
    });

    // Video Box (Youtube, Vimeo, ...)
    $("a[data-popup=frame-video]").colorbox({
        innerWidth: 525,
        innerHeight: 444,
        iframe: true
    });

    // Box Popup (iframe)
    $("a[data-popup=frame]").colorbox({
        width: '100%',
        maxWidth: '600px',
        height: '680px',
        iframe: true,
        close: 'esc'
    });

    // Block popup page (iframe)
    $("a[data-popup=block-page]").click(function (oEvent) {
        oEvent.preventDefault();
        $.get($(this).attr("href"), function (oData) {
            $.colorbox({
                width: '100%',
                maxWidth: '400px',
                maxHeight: '85%',
                html: $(oData).find('#block_page')
            })
        })
    });

    // Classic Box Popup (no frame)
    $("a[data-popup=classic]").colorbox({
        scrolling: false
    });

    // Picture Popup (img tag)
    $("a[data-popup=image]").colorbox({
        maxWidth: '85%',
        maxHeight: '85%',
        scrolling: false,
        transition: 'fade',
        photo: true
    });

    // Picture Slideshow Popup (img tag)
    $("a[data-popup=slideshow]").colorbox({
        maxWidth: '95%',
        maxHeight: '95%',
        transition: 'fade',
        rel: 'photo',
        slideshow: true
    });
});

$goBox();

// Setup tooltips
// Title of the links

$('a[title],img[title],abbr[title]').each(function () {
    // "bIsDataPopup" checks that only for links that do not possess the attribute "data-popup", otherwise not the title of the popup (colorbox) cannot appear because of the plugin (tipTip).
    const bIsDataPopup = $(this).data('popup');

    if (!bIsDataPopup) {
        const oE = $(this);
        let pos = "top";

        if (oE.hasClass("tttop")) {
            pos = "top";
        }
        if (oE.hasClass("ttbottom")) {
            pos = "bottom";
        }
        if (oE.hasClass("ttleft")) {
            pos = "left";
        }
        if (oE.hasClass("ttright")) {
            pos = "right";
        }
        oE.tipTip({defaultPosition: pos});

        $(this).tipTip(
            {
                maxWidth: 'auto',
                edgeOffset: 5,
                fadeIn: 400,
                fadeOut: 400,
                defaultPosition: pos
            }
        );
    }
});

// Title of the Forms
$('form input[title],textarea[title],select[title]').each(function () {
    $(this).tipTip({
        activation: 'focus',
        edgeOffset: 5,
        maxWidth: 'auto',
        fadeIn: 0,
        fadeOut: 0,
        delay: 0,
        defaultPosition: 'right'
    })
});

function openBox(sFile) {
    if (sFile) {
        $('div#box').dialog({
            show: 'slide',
            hide: 'puff',
            resizable: false,
            stack: false,
            zIndex: 10999,
            open: function (oEvent) {
                $(this).load(sFile);
            }
        });
    }
}

function loadingImg(iStatus, sIdContainer) {
    if (iStatus) {
        const sHtml = '<img src="' + pH7Url.tplImg + 'icon/loading2.gif" alt="' + pH7LangCore.loading + '" border="0" />';
        $("#" + sIdContainer).html(sHtml);
    } else {
        $("#" + sIdContainer).html('');
    }
}

if ($('div[role=alert]').length) {
    $('div[role=alert]').fadeOut(15000);
}
