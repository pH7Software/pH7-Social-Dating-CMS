<?php
/**
 * @title            Registry Class
 * @desc             Recording data (variables).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          1.2
 */

namespace PH7\Framework\Registry;
defined('PH7') or exit('Restricted access');

/**
 * @final
 * @class Singleton Class
 */
final class Registry
{

    /**
     * @access private
     * @staticvar array $_aData The data array.
     */
    private static $_aData = array();

    /**
     * Import the Singleton trait.
     */
    use \PH7\Framework\Pattern\Singleton;
    /**
     * @internal We do not put a "__construct" and "__clone" "private" because it is already included in the class \PH7\Framework\Pattern\Base that is included in the \PH7\Framework\Pattern\Singleton class.
     */

    /**
     * Get a data in the register.
     *
     * @param string $sName
     * @return string (string | null) If it finds a given, it returns the data, otherwise returns null.
     */
    public function __get($sName)
    {
        if (isset(self::$_aData[$sName]))
            return self::$_aData[$sName];

        return null;
    }

    /**
     * Set a data in the register.
     *
     * @param string $sName
     * @param string $sValue
     * @return void
     */
    public function __set($sName, $sValue)
    {
        self::$_aData[$sName] = $sValue;
    }

}
