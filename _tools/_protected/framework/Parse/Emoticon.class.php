<?php
/**
 * @title            Emoticon Class
 * @desc             Parse the emoticon code.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\Layout\Optimization;
use PH7\Framework\Service\Emoticon as EmoticonService;

class Emoticon extends EmoticonService
{
    /**
     * Parse the contents.
     *
     * @param string $sContents
     * @param bool $bIsDataUri
     *
     * @return string Contents
     */
    public static function init($sContents, $bIsDataUri = true)
    {
        $aEmoticons = static::get();

        foreach ($aEmoticons as $sEmoticonKey => $aEmoticon) {
            $sContents = self::replaceSymbolToImg($sEmoticonKey, $aEmoticon, $sContents, $bIsDataUri);
        }

        return $sContents;
    }

    /**
     * @param string $sEmoticonKey
     * @param array $aEmoticon
     * @param string $sContents
     * @param bool $bIsDataUri
     *
     * @return string
     */
    private static function replaceSymbolToImg($sEmoticonKey, array $aEmoticon, $sContents, $bIsDataUri)
    {
        return str_ireplace(
            static::getCode($aEmoticon),
            '<img src=\'' . self::getImage($sEmoticonKey, $bIsDataUri) . '\' alt=\'' . static::getName($aEmoticon) . '\' />',
            $sContents
        );
    }

    /**
     * @param string $sEmoticonKey
     * @param bool $bIsDataUri
     *
     * @return string
     */
    private static function getImage($sEmoticonKey, $bIsDataUri)
    {
        if ($bIsDataUri) {
            $sSrcImg = Optimization::dataUri(static::getPath($sEmoticonKey), new File);
        } else {
            $sSrcImg = static::getUrl($sEmoticonKey);
        }

        return $sSrcImg;
    }
}
