/*
 * Author:        Pierre-Henry Soria <hello@ph7cms.com>
 * Copyright:     (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

var $validationBox = (function() {
    $.get(pH7Url.base + 'validate-site/main/validationbox', function(oData) {
        if(oData.length > 1) {
            $.colorbox({
                width      :  '450px',
                maxHeight  :  '85%',
                speed      : 500,
                scrolling  : false,
                html       : $(oData).find('#box_block')
            });
       }
   });
});

$validationBox();
