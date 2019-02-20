/*
 * Title:           AJAPH
 * Description:     This class makes use of Ajax easier.
 *
 * Author:          Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:       (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:         General Public License (GPL) 3 or later (http://www.gnu.org/copyleft/gpl.html)
 * Link:            http://github.com/pH-7/AJAPH
 * Version:         1.3
 */

function AJAPH() {

    /***** Constructor *****/

    /*** Properties ***/
    // XMLHttpRequest object
    this.oXhr = null;
    // Default load image
    this._sLoadImage = "data:image/gif;base64,R0lGODlhEAALAPQAAP///wAAANra2tDQ0Orq6gYGBgAAAC4uLoKCgmBgYLq6uiIiIkpKSoqKimRkZL6+viYmJgQEBE5OTubm5tjY2PT09Dg4ONzc3PLy8ra2tqCgoMrKyu7u7gAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCwAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQJCwAAACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQJCwAAACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQJCwAAACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAkLAAAALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkECQsAAAAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAkLAAAALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkECQsAAAAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7AAAAAAAAAAAA";

    try {
        this.oXhr = new XMLHttpRequest;
    }
    catch (oE) {
        try {
            this.oXhr = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (oE) {
            try {
                this.oXhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (oE) {
            }
        }
    }

    this.oXhr.oMe = this;

    /**
     * Send data.
     *
     * @param {String} Request Type. (GET or POST)
     * @param {String} URL (e.g., 'http://website.com/ajax.php')
     * @param {String} Parameters (e.g., 'param1=foo&param2=bar')
     * @param {Boolean} TRUE = Asynchronous, Synchronous otherwise. Default Asynchronous.
     * @return {Object} This
     */
    this.send = function (sRequestType, sUrl, sParams, bAsync) {
        this._sRequestType = sRequestType.toUpperCase();
        this._sUrl = sUrl;
        this._sParams = sParams;
        this._bAsync = (typeof bAsync == "undefined") ? true : bAsync;

        this._checkRequestType();

        if (this._sRequestType == "GET") this._sUrl += "?" + this._sParams;

        try {
            this.oXhr.open(this._sRequestType, this._sUrl, this._bAsync);
            this.oXhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

            (this._sRequestType == "POST") ? this.oXhr.send(this._sParams) : this.oXhr.send(null);
        }
        catch (oE) {
            alert("Cannot connect to server:\n" + oE.toString());
        }

        return this;
    };

    /**
     * Set HTML Response.
     *
     * @param {String} HTML ID element.
     * @return {Void}
     */
    this.setResponseHtml = function (sHtmlId) {
        this._sResponseType = "text";

        this._sContent = document.getElementById(sHtmlId);

        // Only for asynchronous requests
        if (this._bAsync) {
            this.oXhr.onreadystatechange = function () {
                if (this.readyState == 4)
                    this.oMe._sContent.innerHTML = this.oMe._receive();
                else
                    this.oMe.getLoadImage(sHtmlId);
            }
        }
        else {
            // It should not be used onreadystatechange for synchronous queries
            this._sContent.innerHTML = this._receive();
        }
    };

    this.getResponseText = function () {
        this._sResponseType = "text";
        return this._response();
    };

    this.getResponseXml = function () {
        this._sResponseType = "xml";
        return this._response();
    };

    this.getResponseJson = function () {
        this._sResponseType = "json";
        return this._response();
    };

    this.getLoadImage = function (sHtmlId) {
        if (this.oXhr.readyState != 4)
            document.getElementById(sHtmlId).innerHTML = this._getLoadImageHtmlTag();

        return this;
    };

    this.setLoadImage = function (sImg) {
        this._sLoadImage = sImg;
        return this;
    };

    this.getResponseHeaders = function () {
        var sAllHeaders = this.oXhr.getAllResponseHeaders().split("\n");
        var aHeaders = new Array;
        for (var i = 0; i < sAllHeaders.length; i++)
            if (sAllHeaders[i].indexOf(":") >= 0) aHeaders.push(sAllHeaders[i]);

        return aHeaders;
    };

    this.getXhrObject = function () {
        return this.oXhr;
    };


    /********** Private methods **********/

    this._response = function () {
        // Only for asynchronous requests
        if (this._bAsync) {
            this.oXhr.onreadystatechange = function () {
                if (this.readyState == 4)
                    return this.oMe._receive();
            }
        }
        else {
            // It should not be used onreadystatechange for synchronous queries
            return this._receive();
        }
    };

    this._receive = function () {
        // Check the HTTP status code
        if (this.oXhr.status == 200)
            return this._getResponseType();
        else
            alert('Error HTTP status code: ' + this.oXhr.status + ' ' + this.oXhr.statusText);
    };

    this._getResponseType = function () {
        // Check the response type
        switch (this._sResponseType) {
            case 'xml':
                return this.oXhr.responseXML;
                break;

            case 'json':
                return this.oXhr.responseJson;
                break;

            case 'text':
                return this.oXhr.responseText;
                break;

            default:
                alert('Invalid Type! Choose between "xml", "json" or "text"');
        }
    };

    this._checkRequestType = function () {
        // Check the request type
        if (this._sRequestType != "GET" && this._sRequestType != "POST")
            alert('Wrong Type! Choose between "GET" or "POST"');
    };

    this._getLoadImageHtmlTag = function () {
        return '<img src="' + this._sLoadImage + '" alt="Loading..." />';
    };

}
