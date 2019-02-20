/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */


/*
 * This class is written in literal syntax.
 */
var Video = {

    oVideo: document.getElementsByTagName('video')[0],

    playPause: function () {
        (this.oVideo.paused) ? this.oVideo.play() : this.oVideo.pause();
    },

    bigSize: function () {
        this.oVideo.width = 920;
        this.oVideo.height = 600;
    },

    smallSize: function () {
        this.oVideo.width = 400;
        this.oVideo.height = 200;
    },

    normalSize: function () {
        this.oVideo.width = 600;
        this.oVideo.height = 400;
    }

};
