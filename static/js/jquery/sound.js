/*!
 * Title:           Play Sound
 * Description:     HTML5 Audio with <embed> fallback (this embed tag is in any case necessary for IE7 and 8). | jQuery plugin
 *
 * Author:          Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:       (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:         MIT License (http://opensource.org/licenses/mit-license.php)
 * Link:            http://github.com/pH-7/Slim-HTML5-Audio-jQuery-Plugin
 */

(function ($) {
    jQuery.sound = {

        play: function () {
            return jQuery('<audio style="display:none" autoplay="autoplay" src="' + arguments[0] + '"><embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"></audio>').appendTo('body');
        }

    };
})(jQuery);
