/* Browser regression checks for the real PFBC preview; no application data is used. */
(function ($) {
    'use strict';

    async function checkTags(assert) {
        const oForm = document.getElementById('preview_tags');
        oForm.reset();
        await new Promise((resolve) => setTimeout(resolve, 0));
        const oInput = oForm.querySelector('.pfbc-tags-input');
        const oField = oInput.closest('.pfbc-tags');
        const oRequired = oForm.querySelectorAll('.pfbc-tags-input')[1];
        const getValue = () => new FormData(oForm).get('tags');
        function type(sText, oTarget = oInput) {
            oTarget.value = sText;
            oTarget.dispatchEvent(new Event('input', { bubbles: true }));
        }
        function key(sKey, oTarget = oInput, bComposing = false) {
            const oEvent = new KeyboardEvent('keydown', { key: sKey, bubbles: true, cancelable: true, isComposing: bComposing });
            oTarget.dispatchEvent(oEvent);
            return oEvent;
        }
        assert(oField.querySelectorAll('.pfbc-tag').length === 2, 'Saved tags must render as chips.');
        assert(getValue() === 'Travel,Coffee', 'Saved tags must keep the comma-separated format.');
        assert(!oForm.querySelector('.char_counter'), 'Tags must not show character counters.');
        assert(oInput.labels[0].textContent === 'Article tags', 'The label must target the editable input.');
        assert(!oRequired.checkValidity(), 'An empty required tag field must remain required.');
        type('Music');
        assert(getValue() === 'Travel,Coffee,Music', 'Pending text must be included even before Enter.');
        assert(key('Enter').defaultPrevented, 'Enter must add tags without submitting the form.');
        assert(oField.querySelectorAll('.pfbc-tag').length === 3 && oInput.value === '', 'Enter must create a real chip.');
        assert(key('Enter').defaultPrevented, 'Empty Enter must not submit the form.');
        type('Music');
        key('Enter');
        assert(oField.querySelectorAll('.pfbc-tag').length === 3, 'Exact duplicate tags must not be added.');
        type(' weekend trips, art, ');
        assert(getValue() === 'Travel,Coffee,Music,weekend trips,art', 'Pasted lists must trim separators but preserve spaces inside tags.');
        const sUnsafe = '<img src=x onerror=alert(1)>';
        type(sUnsafe);
        key('Enter');
        assert(!oField.querySelector('img') && oField.textContent.includes(sUnsafe), 'Tag labels must be literal text, never executable HTML.');
        const oRemove = oField.querySelector('button');
        assert(oRemove.type === 'button' && oRemove.getAttribute('aria-label') === 'Remove tag: Travel', 'Remove buttons need accessible names and must not submit.');
        oRemove.click();
        assert(!getValue().includes('Travel'), 'Removing a chip must update its submitted value.');
        key('Backspace');
        assert(document.activeElement === oField.querySelector('.pfbc-tag:last-child button'), 'Backspace must focus removal before deleting a tag.');
        type('京都');
        assert(!key('Enter', oInput, true).defaultPrevented && oInput.value === '京都', 'IME Enter must not prematurely commit a tag.');
        key('Enter');
        assert(getValue().endsWith(',京都'), 'Unicode tags must be preserved.');
        type('a'.repeat(192));
        key('Enter');
        assert(!oInput.checkValidity() && oInput.value.length === 192, 'Overlong lists must show an error without losing input.');
        oInput.focus();
        assert(getComputedStyle(oInput).borderTopWidth === '0px' && oField.classList.contains('pfbc-tags-invalid'), 'Validation must highlight the whole tag field without a second inner border.');
        assert(oField.nextElementSibling.textContent.includes('Shorten or remove a tag'), 'Overlong lists need an actionable message, not a counter.');
        type('Shorter');
        assert(oInput.checkValidity(), 'Shortening an overlong list must clear the error.');
        oInput.dispatchEvent(new FocusEvent('blur'));
        assert(oInput.value === '' && getValue().endsWith(',Shorter'), 'Leaving the field must commit the pending tag.');
        type('🌷'.repeat(20), oRequired);
        key('Enter', oRequired);
        assert(oRequired.checkValidity() && !oRequired.required, 'Committed tags must satisfy required without requiring another tag.');
        type('x', oRequired);
        assert(!oRequired.checkValidity(), 'The limit must include existing tags and comma separators.');
        type('', oRequired);
        assert(oRequired.checkValidity(), 'Clearing excess pending text must restore validity.');
        assert(new FormData(oForm).get('readonly_tags') === 'Saved tag', 'Read-only tags must still submit.');
        assert(!new FormData(oForm).has('disabled_tags'), 'Disabled tags must not submit.');
        assert(oForm.querySelectorAll('.pfbc-tag-remove:disabled').length === 2, 'Disabled/read-only tags must not be removable.');
        oInput.disabled = true;
        await new Promise((resolve) => setTimeout(resolve, 0));
        assert(!new FormData(oForm).has('tags'), 'Disabling an enhanced field must disable its submitted value.');
        oInput.disabled = false;
        await new Promise((resolve) => setTimeout(resolve, 0));
        pH7TagField.init(oInput);
        assert(oField.querySelectorAll('input[type=hidden]').length === 1, 'Re-initialization must not duplicate the submitted value.');
        oForm.reset();
        await new Promise((resolve) => setTimeout(resolve, 0));
        assert(getValue() === 'Travel,Coffee' && oInput.value === '', 'Reset must restore saved tags without a duplicate draft.');
        assert(oField.querySelectorAll('.pfbc-tag').length === 2, 'Reset must restore the initial chips.');
        assert(!oRequired.checkValidity(), 'Reset must restore the required constraint.');
        type('a'.repeat(170));
        key('Enter');
        assert(oField.querySelectorAll('.pfbc-tag').length === 3, 'Long valid tags must not be truncated.');
        assert(oField.scrollWidth <= oField.clientWidth, 'Long tags must wrap inside the field, including on mobile.');
        oForm.reset();
        await new Promise((resolve) => setTimeout(resolve, 0));
    }

    $('#run_checks').on('click', async function () {
        $(this).prop('disabled', true);
        const aFailures = [];
        let iAssertions = 0;
        function assert(bCondition, sMessage) {
            ++iAssertions;
            if (!bCondition) {
                aFailures.push(sMessage);
            }
        }

        try {
            await checkTags(assert);

            const $oJoin = $('#preview_join');
            const $oLoginButton = $('#preview_login button[type=submit]');
            const $oJoinButton = $oJoin.find('button[type=submit]');
            const $oAgreement = $oJoin.find('input[name="agree[]"]');
            $oAgreement.prop('checked', false).trigger('change');
            assert(!$oLoginButton.prop('disabled'), 'Agreement must not disable login.');
            assert($oJoinButton.prop('disabled'), 'Agreement must disable signup.');
            assert($oJoinButton.button('option', 'disabled'), 'The button widget must track the disabled state.');
            $oAgreement.prop('checked', true).trigger('change');
            assert(!$oJoinButton.prop('disabled'), 'Agreement must re-enable signup.');
            assert(!$oJoinButton.button('option', 'disabled'), 'The button widget must recover.');

            const $oPassword = $oJoin.find('.pwd_field input');
            const $oToggle = $oJoin.find('.pwd_toggle');
            $oPassword.val('local-preview');
            $oToggle.trigger('click');
            assert($oPassword.attr('type') === 'text', 'Password reveal must work.');
            assert($oToggle.attr('aria-pressed') === 'true', 'Password reveal must announce its state.');
            $oToggle.trigger('click');
            assert($oPassword.attr('type') === 'password', 'Password hide must work.');
            assert($oPassword.val() === 'local-preview', 'Toggling must preserve the password.');

            const $oAge = $oJoin.find('input[name=age]');
            const $oDistance = $oJoin.find('input[name=distance]');
            assert($oAge.attr('type') === 'range' && $oDistance.attr('type') === 'range', 'Render native sliders, not text boxes.');
            assert($oAge.attr('min') === '18' && $oAge.attr('max') === '99', 'Preserve the configured age bounds.');
            assert(document.getElementById($oAge.attr('id') + '_output').value === '30', 'The age counter must show its initial value.');
            $oAge.val('42').trigger('input');
            assert(document.getElementById($oAge.attr('id') + '_output').value === '42', 'The age counter must follow the slider.');
            assert(document.getElementById($oDistance.attr('id') + '_output').value === '10', 'Updating one slider must not change another counter.');
            $oAge.val('30').trigger('input');

            const $oDescription = $oJoin.find('textarea');
            $oDescription.val('Pasted text').trigger('input');
            assert(document.getElementById($oDescription.attr('id') + '_rem_len').textContent === '11', 'The counter must update on input, including paste.');

            for (const sReply of ['validation', 'failure', 'plain']) {
                const $oAjax = $('#preview_' + sReply);
                const $oButton = $oAjax.find('button[type=submit]');
                const oTagInput = $oAjax.find('.pfbc-tags-input')[0];
                oTagInput.value = 'Pending';
                oTagInput.dispatchEvent(new Event('input', { bubbles: true }));
                assert($oAjax.serialize().includes('tags=Travel%2CPending'), sReply + ': Ajax must include both chips and pending tags.');
                if (sReply === 'validation') {
                    $(document).one('ajaxSuccess', function (oEvent, oRequest, oSettings, oData) {
                        assert(oData.posted_tags === 'Travel,Pending', 'The PHP endpoint must receive the complete comma-separated list.');
                    });
                }
                const oCompleted = new Promise((resolve) => $(document).one('ajaxStop', resolve));
                $oAjax.trigger('submit');
                assert($oButton.prop('disabled'), sReply + ': prevent duplicate submission.');
                // A timeout makes a missing Ajax handler visibly fail instead of hanging the preview.
                await Promise.race([oCompleted, new Promise((resolve) => setTimeout(resolve, 3000))]);
                assert(!$oButton.prop('disabled'), sReply + ': allow retry after the response.');
                assert(sReply === 'plain' ? !$oButton.data('ui-button') : !$oButton.button('option', 'disabled'), sReply + ': preserve the configured button mode.');
                assert($oAjax.find('img.pfbc-loading').length === 0, sReply + ': remove the loading indicator.');
                assert($oAjax.find('.pfbc-error').length === 1, sReply + ': show one actionable error.');
                assert($oAjax.find('.pfbc-error').text().includes(sReply === 'validation' ? 'Please check your email address.' : 'Unable to submit the form. Please try again.'), sReply + ': explain how to recover.');
            }

            assert(document.documentElement.scrollWidth <= innerWidth, 'The page must fit the viewport.');
        } catch (oError) {
            aFailures.push('Unexpected check error: ' + oError.message);
        }
        const sResult = aFailures.length ? aFailures.join(' ') : iAssertions + ' checks passed.';
        $('#preview_result').text(sResult);
        document.documentElement.dataset.auditStatus = aFailures.length ? 'failed' : 'passed';
        document.documentElement.dataset.auditFailures = aFailures.join(' ');
        $(this).prop('disabled', false);
    });

    if (new URLSearchParams(window.location.search).has('audit')) {
        setTimeout(function () {
            $('#run_checks').trigger('click');
        }, 50);
    }
})(jQuery);
