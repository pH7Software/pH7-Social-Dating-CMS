/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

/*
 * ATTENTION!
 * Please replace "ph7cms" with your username after registering at: https://www.geonames.org/login
 * Then enable the Free Web Services here: https://www.geonames.org/manageaccount
 */
$(document).ready(function () {
    autocompleteCityInit('ph7cms'); // Remplace "ph7cms" by your username!
});

function autocompleteCityInit(sGeonamesUsername) {
    $('#str_city').autocomplete(
        {
            source: function (oRequest, oResponse) {
                var sCountry = $('#str_country').val() || '';
                $.ajax(
                    {
                        url: 'https://secure.geonames.org/searchJSON',
                        dataType: 'jsonp',
                        timeout: 10000,
                        data: {
                            username: sGeonamesUsername,
                            country: sCountry,
                            featureClass: 'P',
                            style: 'full',
                            maxRows: 12,
                            name_startsWith: oRequest.term
                        },
                        success: function (oData) {
                            // Check if "geonames" exists. When the API returns an error message, it won't return "geonames"
                            if (!oData || !Array.isArray(oData.geonames)) {
                                if (oData && oData.status && oData.status.message) {
                                    console.error(oData.status.message); // Display the error message from the API into the browser's log
                                }
                                oResponse([]);
                            } else {
                                oResponse($.map(oData.geonames, function (oItem) {
                                    return {
                                        label: oItem.name + (oItem.adminName1 ? ', ' + oItem.adminName1 : '') + (sCountry ? '' : ', ' + oItem.countryName),
                                        value: oItem.name,
                                        state: oItem.adminName1 || '',
                                        postal_code: oItem.postalcode || ''
                                    };
                                }));
                            }
                        },
                        error: function () {
                            oResponse([]);
                        }
                    });
            },
            select: function (oEvent, oUi) {
                $('#str_state').val(oUi.item.state);
                $('#str_zip_code').val(oUi.item.postal_code);
            }
        });
}
