/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// CONSTANTS
var DEFAULT_BACKGROUND_COLOR = "#FFFFFF";
var DEFAULT_FLASH_VERSION = "10";
var DEFAULT_QUALITY = "high";
var DEFAULT_ALIGNMENT = "center";
var DEFAULT_MENU = "true";
var DEFAULT_NAME = "flash";


/**
 * Display the Flash container.
 *
 * @param {String} SWF file.
 * @param {Integer} Width
 * @param {Integer} Height
 * @param {Object} Params in object.
 * @param {Array} Pairs
 * @return {Void}
 */
function pH7DisplayFlash(sFile, iWidth, iHeight, oParams, aPairs) {
    var sFlashVars = "";
    var bWriteAmp = false;

    for (var i in aPairs) {
        if (bWriteAmp) {
            sFlashVars += "&";
        } else {
            bWriteAmp = true;
        }

        if (window.encodeURIComponent) {
            sFlashVars += i + "=" + encodeURIComponent(aPairs[i]);
        } else {
            sFlashVars += i + "=" + escape(aPairs[i]);
        }
    }

    if (!oParams)
        oParams = new Object();

    if (!oParams.version)
        oParams.version = DEFAULT_FLASH_VERSION;

    if (!oParams.align)
        oParams.align = DEFAULT_ALIGNMENT;

    if (!oParams.bgcolor)
        oParams.bgcolor = DEFAULT_BACKGROUND_COLOR;

    if (!oParams.quality)
        oParams.quality = DEFAULT_QUALITY;

    if (!oParams.menu)
        oParams.menu = DEFAULT_MENU;

    if (!oParams.name)
        oParams.name = DEFAULT_NAME;

    if (!oParams.flashvars)
        oParams.flashvars = sFlashVars;

    if (parseInt(oParams.version.substring(0, 1)) < 6) {
        sFile += "?" + oParams.flashvars;
        oParams.flashvars = "";
    }

    var sObjectParams = "";
    var sEmbedParams = "";
    for (var i in oParams) {
        if (i != "version" && i != "align" && i != "name") {
            sObjectParams += "<param name=" + i + " value=\"" + oParams[i] + "\">\n";
            sEmbedParams += i + "=\"" + oParams[i] + "\" ";
        }
    }

    if (!isFlash())
        document.write('<a href="http://www.adobe.com/go/getflashplayer" rel="nofollow"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"></a>');

    document.write("<objec codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=" + oParams.version + "\"");
    document.write("width=\"" + iWidth + "\" height=\"" + iHeight + "\" id=\"" + oParams.name + "\" align=\"" + oParams.align + "\">\n");
    document.write("<param name=movie value=\"" + sFile + "\">\n");
    document.write(sObjectParams);
    document.write("<embed src=\"" + sFile + "\" width=\"" + iWidth + "\" height=\"" + iHeight + "\" name=\"" + oParams.name + "\" align=\"" + oParams.align + "\" ");
    document.write(sEmbedParams);
    document.write(" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>");
    document.write("</object>");
}

/**
 * Check if Flash is installed/enabled in the Web browser.
 *
 * @return {Boolean} Returns true on success.
 */
function isFlash() {
    var bIsFlash = false; // Default value

    try {
        // IE
        var oActiveX = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
        if (oActiveX) bIsFlash = true;
    }
    catch (oE) {
        if (typeof navigator.mimeTypes["application/x-shockwave-flash"] !== 'undefined') bIsFlash = true;
    }

    return bIsFlash;
}
