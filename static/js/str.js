/*
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

function textCounter(a, b) {
    document.getElementById(b).textContent = document.getElementById(a).value.length;
}

/**
 * Live "characters left" counter, auto-wired to every text field that has a
 * maxlength (bios, messages, blog posts, ...). Progressive enhancement: no
 * markup change needed, and the browser still enforces the limit without JS.
 */
(function ($) {
    'use strict';

    var sLabel = (window.pH7LangCore && pH7LangCore.characters_left) || '%0% characters left';

    function updateCounter($oField, $oCounter) {
        var iMax = parseInt($oField.attr('maxlength'), 10);
        var iLeft = iMax - $oField.val().length;

        $oCounter.text(sLabel.replace('%0%', iLeft));
        $oCounter.toggleClass('char_counter_low', iLeft <= 10);
    }

    $(function () {
        $('textarea[maxlength], input[type=text][maxlength]').each(function () {
            var $oField = $(this);

            // Opt-out hook, and skip fields already counted (e.g. re-init after ajax)
            if ($oField.data('noCounter') || $oField.data('counterBound')) {
                return;
            }

            // PFBC's Textarea element renders its own "N character(s)" counter (via textCounter()
            // above) in a following <span id="<id>_rem_len">; don't add a second one on those.
            if ($oField.attr('id') && document.getElementById($oField.attr('id') + '_rem_len')) {
                return;
            }
            $oField.data('counterBound', true);

            var $oCounter = $('<small>', { 'class': 'char_counter', 'aria-live': 'polite' });
            $oField.after($oCounter);

            updateCounter($oField, $oCounter);
            $oField.on('input', function () {
                updateCounter($oField, $oCounter);
            });
        });
    });
})(jQuery);
