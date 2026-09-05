/* Browser regression checks for the real PFBC preview; no application data is used. */
(function ($) {
    'use strict';

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
            const $oButton = $oAjax.find('button');
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
        $('#preview_result').text(aFailures.length ? aFailures.join(' ') : iAssertions + ' checks passed.');
        $(this).prop('disabled', false);
    });
})(jQuery);
