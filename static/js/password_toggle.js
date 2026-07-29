/*
 * Show/hide toggle for password fields (progressive enhancement).
 * Mistyped masked passwords are the top cause of failed logins on mobile.
 *
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

(function ($) {
    'use strict';

    var EYE_ICON = '👁';

    // Falls back to English when a custom language pack hasn't been updated yet
    var sShowLabel = (window.pH7LangCore && pH7LangCore.show_password) || 'Show password';
    var sHideLabel = (window.pH7LangCore && pH7LangCore.hide_password) || 'Hide password';

    $(function () {
        $('input[type=password]').each(function () {
            var $oInput = $(this);

            // Opt-out hook for special fields
            if ($oInput.data('noToggle')) {
                return;
            }

            var $oButton = $('<button>', {
                type: 'button',
                'class': 'pwd_toggle',
                'aria-label': sShowLabel,
                'aria-pressed': 'false',
                title: sShowLabel
            });
            // The emoji is decorative; aria-hidden keeps screen readers from announcing it
            // on top of the aria-label (which would read e.g. "Show password, eye").
            $oButton.append($('<span>', { 'aria-hidden': 'true', text: EYE_ICON }));

            $oButton.on('click', function () {
                var bReveal = $oInput.attr('type') === 'password';
                $oInput.attr('type', bReveal ? 'text' : 'password');
                $oButton
                    .attr('aria-pressed', bReveal ? 'true' : 'false')
                    .attr('aria-label', bReveal ? sHideLabel : sShowLabel)
                    .attr('title', bReveal ? sHideLabel : sShowLabel)
                    .toggleClass('pwd_toggle_active', bReveal);
            });

            $oInput.after($oButton);
        });
    });
})(jQuery);
