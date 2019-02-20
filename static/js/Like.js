/*!
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// This feature is only for members!
var Like = {

    evenements: function () {
        $('a#like').click(this.click);
    },

    precharger: function () {
        var aLikes = $('a#like');
        for (var i = 0, l = aLikes.length; i < l; i++) {
            $.ajax({
                context: aLikes[i],
                type: 'POST',
                dataType: 'json',
                url: pH7Url.base + 'asset/ajax/Like/',
                data: 'key=' + encodeURIComponent($(aLikes[i]).data('key')),
                success: function (oData) {
                    var iVotes = parseInt(oData.votes);
                    if (isNaN(iVotes)) {
                        iVotes = '-1';
                    }
                    if (iVotes > 0) {
                        $(this).html(pH7LangCore.like + ' <em>(' + iVotes + ')</em>');
                    } else {
                        $(this).html(pH7LangCore.like);
                    }
                }
            });
        }
    },

    click: function (oEvent) {
        var oElement = oEvent.currentTarget;
        $(oElement).unbind('click'); // Stop multiple clicks
        $(oElement).click(function () {
            return false;
        }); // Blocking link

        $.ajax({
            context: oElement,
            type: 'POST',
            dataType: 'json',
            url: pH7Url.base + 'asset/ajax/Like/',
            data: 'vote=1&key=' + encodeURIComponent($(oElement).data('key')),
            success: function (oData) {
                $(this).css({'opacity': 0});
                $(this).addClass('like_voted');
                var iVotes = parseInt(oData.votes);
                if (isNaN(iVotes)) {
                    iVotes = '-1';
                }
                $(this).html(oData.txt);
                $(this).animate({'opacity': 1}, {'duration': 'slow'});
            },
            error: function () {
                $('.msg').addClass('alert alert-danger').text(pH7LangCore.misloading).delay(2000).fadeOut();
            }
        });
        return false;
    }

};

Like.evenements();
Like.precharger();
