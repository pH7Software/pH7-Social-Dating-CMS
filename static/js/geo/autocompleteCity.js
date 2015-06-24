/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

/*
 * ATTENTION!
 * Please remplace "ph7cms" by your username by registering here: http://www.geonames.org/login
 * After, you will need to enable the Web service here: http://www.geonames.org/manageaccount
 */

var sGeonamesUsername = 'ph7cms'; // Remplace "ph7cms" by your username!


function autocompleteCityInit(sGeonamesUsername)
{
    // Set the variables
    var sCountry = $('#str_country').val();
    var sUrlSlug = (typeof sCountry != 'undefined' ? '?country=' + sCountry : '');

    $('#str_city').autocomplete(
    {
        source: function(oRequest, oResponse)
        {
            $.ajax(
            {
                url: 'http://ws.geonames.org/searchJSON' + sUrlSlug + '&username=' + sGeonamesUsername,
                dataType: 'jsonp',
                data:
                {
                    featureClass: 'P',
                    style: 'full',
                    maxRows: 12,
                    name_startsWith: oRequest.term
                },
                success: function(oData)
                {
                    oResponse($.map(oData.geonames, function(oItem)
                    {
                        $('#str_city').click(function()
                        {
                            $('#str_state').val((oItem.adminName1 ? oItem.adminName1 : ''));
                            $('#str_zip_code').val(oItem.postalcode);
                        });

                        return
                        {
                            label: oItem.name + (oItem.adminName1 ? ', ' + oItem.adminName1 : '') + (sCountry ? '' : ', ' + oItem.countryName),
                            value: oItem.name
                        }
                    }))
                }
            })
        }
    })
}


$(document).ready(function()
{
    autocompleteCityInit();
});