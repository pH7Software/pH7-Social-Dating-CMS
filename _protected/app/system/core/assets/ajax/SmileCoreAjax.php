<?php
/**
 * @title          Smile Ajax
 * @desc           Get Smiles Ajax in JSON format.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;
use PH7\Framework\Service\Emoticon;

class SmileCoreAjax extends Emoticon
{
    const CACHE_LIFETIME = 120 * 48 * 30;

    /** @var string */
    private static $sData = '';

    /**
     * @return void
     *
     * @throws Framework\Http\Exception
     */
    public static function output()
    {
        static::retrieve();
        Http::setContentType('application/json');

        echo self::$sData;
    }

    private static function retrieve()
    {
        $oCache = (new Cache)->start('str/json', 'emoticons', self::CACHE_LIFETIME);

        if (!self::$sData = $oCache->get()) {
            $aEmoticons = static::get();

            foreach ($aEmoticons as $sEmoticonKey => $aEmoticon) {
                $mCode = static::getCode($aEmoticon);
                $sImg = static::getUrl($sEmoticonKey);
                $sName = static::getName($aEmoticon);

                $sCode = (is_array($mCode)) ? $mCode[0] : $mCode;

                self::$sData .= <<<EOD
                {
                    "code": "$sCode",
                    "img": "$sImg",
                    "name": "$sName"
                },
EOD;
            }

            self::$sData = '{"smiles": [' . substr(self::$sData, 0, -1) . ']}';
            $oCache->put(self::$sData);
        }
        unset($oCache);
    }
}

// Output
SmileCoreAjax::output();



