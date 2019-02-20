/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */
/*
 * This code was inspired by Martin Angelov's tutorial: http://tutorialzine.com/2011/04/jquery-webcam-photobooth/
 */

var sShowCss = 'display:block !important;visibility:visible !important';
var sHideCss = 'display:none !important;visibility:none !important';

var oCamera = $('#camera'),
    oPhotos = $('#photos'),
    oScreen = $('#screen');

var sTemplate = '<a href="{url}system/modules/webcam/picture/img/original/{src}" class="cam" '
    + 'style="background-image:url({url}system/modules/webcam/picture/img/thumb/{src})"></a>';

/*----------------------------------
 Setting up the web camera
 ----------------------------------*/


webcam.set_swf_url(pH7Url.tplMod + 'webcam.swf');
webcam.set_api_url(pH7Url.base + 'webcam/asset/ajax/UploadPicture'); // The upload script
webcam.set_quality(80);             // JPEG Photo Quality
webcam.set_shutter_sound(true, pH7Url.tplMod + 'shutter.mp3');

// Generating the embed code and adding it to the page:
oScreen.html(
    webcam.get_html(oScreen.width(), oScreen.height())
);


/*----------------------------------
 Binding event listeners
 ----------------------------------*/


var shootEnabled = false;

$('#shootButton').click(function () {

    if (!shootEnabled) {
        return false;
    }

    webcam.freeze();
    togglePane();
    return false;
});

$('#cancelButton').click(function () {
    webcam.reset();
    togglePane();
    return false;
});

$('#uploadButton').click(function () {
    webcam.upload();
    webcam.reset();
    togglePane();
    return false;
});

oCamera.find('.settings').click(function () {
    if (!shootEnabled) {
        return false;
    }

    webcam.configure('camera');
});

// Showing and hiding the camera panel:

var shown = false;
$('.camTop').click(function () {

    $('.tooltip').fadeOut('fast').attr('style', sHideCss);

    if (shown) {
        oCamera.animate({
            bottom: -466
        });
    }
    else {
        oCamera.animate({
            bottom: -5
        }, {easing: 'easeOutExpo', duration: 'slow'});
    }

    shown = !shown;
});

$('.tooltip').mouseenter(function () {
    $(this).fadeOut('fast').attr('style', sHideCss);
});


/*----------------------
 Callbacks
 ----------------------*/


webcam.set_hook('onLoad', function () {
    // When the flash loads, enable
    // the Shoot and settings buttons:
    shootEnabled = true;
});

webcam.set_hook('onComplete', function (msg) {

    // This response is returned by upload
    // and it holds the name of the image in a
    // JSON object format:

    msg = $.parseJSON(msg);

    if (msg.error) {
        alert(msg.message);
    }
    else {
        // Adding it to the page;
        oPhotos.prepend(templateReplace(sTemplate, {url: pH7Url.data, src: msg.filename}));
        initBox();
    }
});

webcam.set_hook('onError', function (e) {
    oScreen.html(e);
});


/*-------------------------------------
 Populating the page with images
 -------------------------------------*/

var start = '';

function loadPics() {

    // This is true when loadPics is called
    // as an event handler for the LoadMore button:

    if (this != window) {
        if ($(this).html() == 'Loading..') {
            // Preventing more than one click
            return false;
        }
        $(this).html('Loading..');
    }

    // Issuing an AJAX request. The start parameter
    // is either empty or holds the name of the first
    // image to be displayed. Useful for pagination:

    $.getJSON(pH7Url.base + 'webcam/asset/ajax/BrowsePicture/', {'start': start}, function (r) {

        oPhotos.find('a').attr('style', sShowCss);
        var loadMore = $('#loadMore').detach();

        if (!loadMore.length) {
            loadMore = $('<span>', {
                id: 'loadMore',
                html: 'Load More',
                click: loadPics
            });
        }

        $.each(r.files, function (i, filename) {
            oPhotos.append(templateReplace(sTemplate, {url: pH7Url.data, src: filename}));
        });

        // If there is a next page with images:
        if (r.nextStart) {

            // r.nextStart holds the name of the image
            // that comes after the last one shown currently.

            start = r.nextStart;
            oPhotos.find('a:last').attr('style', sHideCss);
            oPhotos.append(loadMore.html('Load More'));
        }

        // We have to re-initialize the box every
        // time we add new photos to the page:

        initBox();
    });

    return false;
}

// Automatically calling loadPics to
// populate the page onload:

loadPics();


/*----------------------
 Helper functions
 ------------------------*/


// This function initializes the
// box lightbox script.

function initBox(filename) {
    oPhotos.find('a:visible').colorbox({
        maxWidth: '95%',
        maxHeight: '95%',
        rel: 'cam',
        slideshow: true
    });
}


// This function toggles the two
// .buttonPane divs into visibility:

function togglePane() {
    var visible = $('#camera .buttonPane:visible:first');
    var hidden = $('#camera .buttonPane:hidden:first');

    visible.attr('style', sHideCss).fadeOut('fast', function () {
        hidden.attr('style', sShowCss);
    });
}


// Helper function for replacing "{KEYWORD}" with
// the respectful values of an object:

function templateReplace(oContent, aData) {
    return oContent.replace(/{([^}]+)}/g, function (oMatch, oGroup) {
        return aData[oGroup.toLowerCase()];
    });
}
