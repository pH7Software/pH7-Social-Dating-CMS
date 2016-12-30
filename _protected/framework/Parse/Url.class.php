<?php
/**
 * @title            Url Parser Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.1
 */

namespace PH7\Framework\Parse;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str;

class Url
{

    /**
     * Clean URL.
     *
     * @static
     * @param string $sUrl
     * @param boolean $bFullClean Also removes points, puts characters to lowercase, etc. Default TRUE
     * @return string The new clean URL
     */
    public static function clean($sUrl, $bFullClean = true)
    {
        $sUrl = preg_replace( '/[\s]+/', '-', $sUrl);
        $sUrl = str_replace(array('«', '»', '"', '~', '#', '$', '@', '`', '§', '$', '£', 'µ', '\\', '[', ']', '<', '>', '%', '*', '{', '}'), '-', $sUrl);

        if ($bFullClean)
        {
            $sUrl = str_replace(array('.', '^', ',', ':', ';', '!'), '', $sUrl);
            $oStr = new Str;
            $sUrl = $oStr->lower($sUrl);
            $sUrl = $oStr->escape($sUrl, true);
            unset($oStr);
        }

        return $sUrl;
    }

    /**
     * Gets the name of a URL.
     *
     * @static
     * @param string $sLink The link
     * @return string The name of the domain with the first letter capitalized.
     */
    public static function name($sLink)
    {
        $oStr = new Str;
        $sLink = $oStr->upperFirst(preg_replace('#(^https?://|www\.|\.[a-z]{2,4}/?$)#i', '', $oStr->lower($sLink)));
        unset($oStr);

        return $sLink;
    }

}
