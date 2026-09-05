/* Isolated browser fixture: mock responses exercise the real application scripts. */
window.pH7Url = {base: '/preview/'};
(function ($) {
    'use strict';

    let sReply = 'success';
    const aRequests = [];
    $.ajax = function (oOptions) {
        aRequests.push(oOptions);
        setTimeout(function () {
            if (sReply === 'failure') {
                if (oOptions.error) oOptions.error({}, 'error');
                if (oOptions.complete) oOptions.complete({}, 'error');
                return;
            }
            let mResponse;
            if (oOptions.url.includes('autocompleteUsername')) {
                const sAvatar = location.origin + '/templates/themes/base/img/icon/visitor_no_picture-32.svg';
                mResponse = '<users><ul><li><username>alex</username><avatar>' + sAvatar + '</avatar></li>' +
                    '<li><username>sam</username><avatar>' + sAvatar + '</avatar></li>' +
                    '<li><username>&lt;b&gt;literal&lt;/b&gt;</username><avatar>javascript:void(0)</avatar></li></ul></users>';
            } else if (oOptions.url.includes('geonames.org')) {
                mResponse = sReply === 'malformed' ? {} : {geonames: [
                    {name: 'Sydney', adminName1: 'New South Wales', postalcode: '2000', countryName: 'Australia'},
                    {name: 'Melbourne', adminName1: 'Victoria', countryName: 'Australia'}
                ]};
            } else {
                throw new Error('Unexpected request in isolated autocomplete preview.');
            }
            if (oOptions.success) oOptions.success(mResponse);
            if (oOptions.complete) oOptions.complete({responseText: mResponse}, 'success');
        }, 0);
    };

    $(function () {
        $('#run_autocomplete_checks').on('click', async function () {
            $(this).prop('disabled', true);
            let iAssertions = 0;
            const aFailures = [];
            function assert(bCondition, sMessage) {
                ++iAssertions;
                if (!bCondition) aFailures.push(sMessage);
            }
            async function search($oField) {
                const oResponse = new Promise((resolve) => $oField.one('autocompleteresponse', resolve));
                $oField.trigger('focus').autocomplete('search', 's');
                await Promise.race([oResponse, new Promise((resolve) => setTimeout(resolve, 1000))]);
            }

            const $oRecipient = $('#recipient');
            sReply = 'success';
            await search($oRecipient);
            const $oUsers = $oRecipient.autocomplete('widget');
            assert($oUsers.find('.ui-menu-item').length === 3, 'Render every suggested recipient.');
            assert($oUsers.find('img').length === 2, 'Render avatars with current jQuery UI markup.');
            assert($oUsers.find('b').length === 0, 'Treat usernames as text.');
            assert($oUsers.find('img[src^="javascript:"]').length === 0, 'Ignore unsafe avatar URLs.');
            const oBounds = $oUsers[0].getBoundingClientRect();
            assert(oBounds.left >= 0 && oBounds.right <= innerWidth, 'Keep recipient suggestions inside the viewport.');
            $oUsers.find('.ui-menu-item-wrapper').first().trigger('click');
            assert($oRecipient.val() === 'alex', 'Selecting a recipient must fill the field.');
            sReply = 'failure';
            await search($oRecipient);
            assert($oRecipient.autocomplete('instance').pending === 0, 'Clear recipient loading state after failure.');

            const $oCity = $('#str_city');
            $('#str_country').val('AU');
            $('#str_state').val('Unchanged');
            sReply = 'success';
            await search($oCity);
            assert(aRequests[aRequests.length - 1].data.country === 'AU', 'Read the country without requiring a mouse click.');
            $oCity.trigger('mousemove');
            assert($('#str_state').val() === 'Unchanged', 'Do not copy details from unselected cities.');
            $oCity.autocomplete('widget').find('.ui-menu-item-wrapper').first().trigger('click');
            assert($('#str_state').val() === 'New South Wales' && $('#str_zip_code').val() === '2000', 'Use the selected city details.');
            await search($oCity);
            $oCity.autocomplete('widget').find('.ui-menu-item-wrapper').last().trigger('click');
            assert($('#str_state').val() === 'Victoria' && $('#str_zip_code').val() === '', 'Clear a previous postcode when the selected city has none.');
            for (const sFailure of ['malformed', 'failure']) {
                sReply = sFailure;
                await search($oCity);
                assert($oCity.autocomplete('instance').pending === 0, sFailure + ': clear city loading state.');
            }
            sReply = 'success';
            assert(document.documentElement.scrollWidth <= innerWidth, 'Avoid horizontal page overflow.');
            $('#autocomplete_result').text(aFailures.length ? aFailures.join(' ') : iAssertions + ' checks passed.');
            $(this).prop('disabled', false);
        });
    });
})(jQuery);
