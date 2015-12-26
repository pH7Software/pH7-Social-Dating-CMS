<?php
/**
 * @title            Static Method Cache Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache / OOP / Methods
 */

namespace PH7\Framework\Cache\OOP\Methods;
defined('PH7') or exit('Restricted access');

class Stic implements Methods
{
    /**
     * @staticvar array $aStaticMethods Store methods into an array.
     */
    private static $aStaticMethods = [];

    /**
     * @param string $sMethod
     * @param array $aArgs
     * @return mixed The method contents
     */
    public function __callStatic($sMethod, array $aArgs)
    {
        $sCalledClass = static::class;
        $sHash = md5(serialize($aArgs));

        if (substr($sMethod,0,3) === static::METHOD_PREFIX) {
            if ((new \ReflectionClass(static))->hasMethod($sMethod)) {
                return self::$aStaticMethods[$sCalledClass][$sMethod][$sHash] = (new \ReflectionMethod(static, $sMethod))->invokeArgs(new $sCalledClass, $aArgs);
            } else {
                // Cache is disabled or need a refresh for this method
                $sMethod = substr($sMethod, 3);
                return (new \ReflectionMethod(static, $sMethod))->invokeArgs(new $sCalledClass, $aArgs);
            }
        }

        $sCachedMethod = static::METHOD_PREFIX . $sMethod;
        if ((new \ReflectionClass(static))->hasMethod($sCachedMethod)) {
            if (empty(self::$aStaticMethods[$sCalledClass][$sCachedMethod]) && !array_key_exists($sHash, self::$aStaticMethods[$sCalledClass][$sCachedMethod])) {
                    self::$aStaticMethods[$sCalledClass][$sCachedMethod][$sHash] = (new \ReflectionMethod(static, $sCachedMethod))->invokeArgs(new $sCalledClass, $aArgs);
            }
            return self::$aStaticMethods[$sCalledClass][$sCachedMethod][$sHash];
        }
    }
}
