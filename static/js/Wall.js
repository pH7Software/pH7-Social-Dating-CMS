/*!
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * Version:       0.8
 */

// This feature is only for members!

/**
 * @class Wall
 */
function Wall() {
    /**
     * @property oMe
     * @type {Object} This
     */
    var oMe = this;

    /**
     * @property sUrl
     * @type {String}
     */
    this.sUrl = 'user/asset/ajax/Wall';

    /**
     * Show wall messages.
     *
     * @async
     */
    this.show = function () {
        $.post(pH7Url.base + this.sUrl, {type: 'show'}).success(function (sHtmlData) {
            $('#wall').html(sHtmlData);
        }).error(function () {
            $('.msg').addClass('alert alert-danger').text(pH7LangCore.unable_retrive_feeds).delay(2000).fadeOut();
        });
        setTimeout(function () {
            oMe.show()
        }, 5000);
    };

    /**
     * Show comments.
     *
     * @async
     */
    this.showComment = function () {
        $.post(pH7Url.base + this.sUrl, {type: 'showCommentProfile'}).success(function (sHtmlData) {
            $('#wall').html(sHtmlData);
        }).error(function () {
            $('.msg').addClass('alert alert-danger').text(pH7LangCore.unable_retrive_feeds).delay(2000).fadeOut();
        });
        setTimeout(function () {
            oMe.showComment()
        }, 5000);
    };

    /**
     * Add a wall message.
     *
     * @async
     */
    this.add = function () {
        var sPost = $('.wall_post').val();

        $.post(pH7Url.base + this.sUrl, {type: 'add', 'post': sPost}, function (oData) {
            oMe._output(oData);
        }, 'json');
    };


    /**
     * Edit a wall message.
     *
     * @async
     */
    this.edit = function () {
        var sPost = $('.wall_post').val();

        $.post(pH7Url.base + this.sUrl, {type: 'edit', 'post': sPost}, function (oData) {
            oMe._output(oData);
        }, 'json');
    };


    /**
     * Delete a wall message.
     *
     * @async
     */
    this.del = function () {
        var sPost = $('.wall_id').val();

        $.post(pH7Url.base + this.sUrl, {type: 'delete', 'post': sPost}, function (oData) {
            oMe._output(oData);
        }, 'json');
    };

    /**
     * Output comments.
     *
     * @param {Object} Data.
     * @return {Void} Set data to HTML contents.
     */
    this._output = function (oData) {
        if (oData.status == 1) {
            $('.msg').addClass('alert alert-success');
            $('#wall_' + oData.msgId).hide('slow');
        } else {
            $('.msg').addClass('alert alert-danger');
        }
        $('.msg').text(oData.txt).delay(2000).fadeOut();
    };
}

var oWall = new Wall;
// oWall.show();
oWall.showComment();
