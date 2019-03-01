/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

/*
 * ATTENTION!
 * Please remplace "ph7cms" by your username by registering on: http://www.geonames.org/login
 * After, you will need to enable the Free Web Services here: http://www.geonames.org/manageaccount
 */
$(document).ready(function () {
    autocompleteCityInit('ph7cms'); // Remplace "ph7cms" by your username!
});

function autocompleteCityInit(sGeonamesUsername) {
    // Set "country" API parameter
    var sUrlSlug = '';
    $('#str_city').click(function () {
        sUrlSlug = '&country=' + $('#str_country').val();
    });

    $('#str_city').autocomplete(
        {
            source: function (oRequest, oResponse) {
                $.ajax(
                    {
                        url: 'http://ws.geonames.org/searchJSON?username=' + sGeonamesUsername + sUrlSlug,
                        dataType: 'jsonp',
                        data: {
                            featureClass: 'P',
                            style: 'full',
                            maxRows: 12,
                            name_startsWith: oRequest.term
                        },
                        success: function (oData) {
                            // Check if "geonames" exists. When the API returns an error message, it won't return "geonames"
                            if (!oData.geonames) {
                                if (oData.status.message) {
                                    console.error(oData.status.message); // Display the error message from the API into the browser's log
                                }
                            } else {
                                oResponse($.map(oData.geonames, function (oItem) {
                                    $('#str_city').mousemove(function () {
                                        $('#str_state').val((oItem.adminName1 ? oItem.adminName1 : ''));
                                        $('#str_zip_code').val(oItem.postalcode);
                                    });

                                    return {
                                        label: oItem.name + (oItem.adminName1 ? ', ' + oItem.adminName1 : '') + (sUrlSlug.trim() ? '' : ', ' + oItem.countryName),
                                        value: oItem.name
                                    }
                                }));
                            }
                        }
                    })
            }
        })
}
