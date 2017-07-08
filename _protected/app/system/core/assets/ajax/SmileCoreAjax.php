<?php
/**
 * @title          Smile Ajax
 * @desc           Get Smiles Ajax in JSON format.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;

class SmileCoreAjax extends \PH7\Framework\Service\Emoticon
{

    private static $_sData = '';

    public static function output()
    {
        static::_get();

        Http::setContentType('application/json');
        echo static::$_sData;
    }

    private static function _get()
    {
        $oCache = (new Cache)->start('str/json', 'emoticons', 120 * 48 * 30);

        if (!static::$_sData = $oCache->get()) {
            $aEmoticons = static::get();

            foreach ($aEmoticons as $sEmoticonKey => $aEmoticon) {
                $mCode = static::getCode($aEmoticon);
                $sImg = static::getUrl($sEmoticonKey);
                $sName = static::getName($aEmoticon);

                $sCode = (is_array($mCode)) ? $mCode[0] : $mCode;

                static::$_sData .= <<<EOD
                {
                    "code": "$sCode",
                    "img": "$sImg",
                    "name": "$sName"
                },
EOD;
            }

            static::$_sData = '{"smiles": [' . substr(static::$_sData, 0, -1) . ']}';
            $oCache->put(static::$_sData);
        }
        unset($oCache);
    }

}

// Output
SmileCoreAjax::output();



