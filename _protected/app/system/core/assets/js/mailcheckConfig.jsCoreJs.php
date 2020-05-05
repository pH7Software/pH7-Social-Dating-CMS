<?php
/**
 * @title          Mail Check File
 * @desc           This file allows suggests a right domain when your users misspell it in an email address.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Js
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Compress\Compress;
use PH7\Framework\Config\Config;
use PH7\Framework\Service\Suggestion;

$oCache = (new Cache)->start(
    'str/js',
    'mailcheckConfig',
    120 * 48 * 30
);

if (!$sData = $oCache->get()) {
    $sData = '
    var domains = [\'' . Suggestion::email() . '\'];
    $(\'input[id^=email]\').blur(function(){
        var input = $(this);
        var parent = input.parents(\'pfbc-textbox\');
        input.mailcheck({
            domains : domains,
            suggested: function(element, suggestion){
                input.next(\'span\').remove();
                $(\'<span class="warn_msg"/>\').fadeIn(\'slow\').insertAfter(input).append(\'' . t('Did you mean %0%?', '<a href="#">\'+suggestion.address.substring(0,' . PH7_MAX_EMAIL_LENGTH . ')+\'@\'+\'<strong>\'+suggestion.domain+\'</strong></a>') . '\').find(\'a\').click(function(e){
                    e.preventDefault();
                    input.val($(this).text());
                    input.trigger(\'blur\');
                });
            },
            empty : function(element){
                input.next(\'span\').remove();
            }
        })
    });';

    if (Config::getInstance()->values['cache']['enable.static.minify']) {
        // Compression of JavaScript Code
        $sData = (new Compress)->parseJs($sData);
    }

    $oCache->put($sData);
}

unset($oCache);
echo $sData;
