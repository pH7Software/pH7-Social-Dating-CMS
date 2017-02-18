<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Service
 */

namespace PH7\Framework\Service;

defined('PH7') or exit('Restricted access');

/**
 * @class Abstract Class
 */
abstract class Emoticon
{
    const DIR = 'smile/', EXT = '.gif';

    /**
     * Gets the list of emoticons.
     *
     * @access protected
     * @static
     * @return array
     */
    protected static function get()
    {
        return include PH7_PATH_APP_CONFIG . 'emoticon.php';
    }

    /**
     * Gets the path of emoticon.
     *
     * @access protected
     * @static
     * @param string $sName
     * @return Emoticon path.
     */
    protected static function getPath($sName)
    {
        return PH7_PATH_STATIC . PH7_IMG . static::DIR . $sName . static::EXT;
    }

    /**
     * Gets the URL of emoticon.
     *
     * @access protected
     * @static
     * @param string $sName
     * @return Emoticon URL.
     */
    protected static function getUrl($sName)
    {
        return PH7_URL_STATIC . PH7_IMG . static::DIR . $sName . static::EXT;
    }

    /**
     * Gets the name of emoticon.
     *
     * @access protected
     * @static
     * @param array $aVal
     * @return Emoticon name.
     */
    protected static function getName($aVal)
    {
        return $aVal[1];
    }

    /**
     * Gets the emoticon code.
     *
     * @access protected
     * @static
     * @param array $aVal
     * @return Emoticon code.
     */
    protected static function getCode($aVal)
    {
        return $aVal[0];
    }
}
