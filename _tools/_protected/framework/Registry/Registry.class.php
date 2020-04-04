<?php
/**
 * @title            Registry Class
 * @desc             Recording data (variables).
 *
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          1.3
 */

namespace PH7\Framework\Registry;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Singleton;

/**
 * @class Singleton Class
 */
final class Registry
{
    /** @var array */
    private static $aData = [];

    /**
     * Import the Singleton trait.
     */
    use Singleton;

    /**
     * @internal We don't add "__construct()" and "__clone"() private methods because it's already done in \PH7\Framework\Pattern\Base which is included in \PH7\Framework\Pattern\Singleton
     */

    /**
     * Get a data in the register.
     *
     * @param string $sName
     *
     * @return string|null If it finds a given, it returns the data, otherwise returns null.
     */
    public function __get($sName)
    {
        if (isset(self::$aData[$sName])) {
            return self::$aData[$sName];
        }

        return null;
    }

    /**
     * Set a data in the register.
     *
     * @param string $sName
     * @param string $sValue
     *
     * @return void
     */
    public function __set($sName, $sValue)
    {
        self::$aData[$sName] = $sValue;
    }
}
