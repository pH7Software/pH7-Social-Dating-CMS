<?php
/**
 * @title          Smile Ajax
 * @desc           Get Smiles Ajax in JSON format.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 */

/**
 * Access to it with: https://YOUR-URL.com/asset/ajax/Smile
 */
namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;
use PH7\Framework\Service\Emoticon;

class SmileCoreAjax extends Emoticon
{
    const CACHE_LIFETIME = 120 * 48 * 30;
    const CONTENT_TYPE = 'application/json';

    /** @var string */
    private static $sData = '';

    /**
     * @return void Output the emoticon JSON code.
     *
     * @throws Framework\Http\Exception
     */
    public static function output()
    {
        static::retrieve();
        Http::setContentType(self::CONTENT_TYPE);

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

                $sCode = is_array($mCode) ? $mCode[0] : $mCode;

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
