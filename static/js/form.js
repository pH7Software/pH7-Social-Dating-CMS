/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

const sButtonPattern = 'button[type=submit]';

function enable_button(oForm) {
    set_submit_disabled(oForm, false);
}

function disable_button(oForm) {
    set_submit_disabled(oForm, true);
}

function set_submit_disabled(oForm, bDisabled) {
    const $oButtons = oForm ? $(oForm).find(sButtonPattern) : $(sButtonPattern);
    $oButtons.each(function () {
        const $oButton = $(this);
        if ($oButton.data('ui-button')) {
            $oButton.button('option', 'disabled', bDisabled);
        } else {
            $oButton.prop('disabled', bDisabled);
        }
    });
}

const sInputAgree = 'input[name="agree[]"]';
$(sInputAgree).on('change', function () {
    this.checked && this.value === '1' ? enable_button(this.form) : disable_button(this.form);
});

$('input[name=all_action]').on('click', function () {
    $('input[name="action[]"]').prop('checked', $(this).is(':checked'));
});

/**
 * Check the checkbox fields.
 *
 * @param {Boolean} [extra=false]. Put FALSE if you do not want the confirmation alert. Default: TRUE
 * @return {Boolean}
 */
function checkChecked(bIsConfirmAlert) {
    if (typeof bIsConfirmAlert == "undefined")
        bIsConfirmAlert = true; // Default value

    let iCountChecked = 0;
    $('input[name="action[]"]').each(function () {
        iCountChecked += $(this).is(':checked');
    });

    if (iCountChecked == 0) {
        alert(pH7LangCore.select_least_one);
        return false;
    }
    if (bIsConfirmAlert) {
        return confirm(pH7LangCore.warning_irreversible_action);
    }

    return true;
}
