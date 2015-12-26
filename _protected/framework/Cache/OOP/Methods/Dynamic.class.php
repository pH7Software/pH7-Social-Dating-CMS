<?php
/**
 * @title            Dynamic Method Cache Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache / OOP / Methods
 */

namespace PH7\Framework\Cache\OOP\Methods;
defined('PH7') or exit('Restricted access');

class Dynamic implements Methods
{
    /**
     * @var array $aStaticMethods Store methods into an array.
     */
    private $aMethods = [];

    /**
     * @param string $sMethod
     * @param array $aArgs
     * @return mixed The method contents
     */
    public function __call($sMethod, array $aArgs)
    {
        $sCalledClass = static::class;
        $sHash = md5(serialize($aArgs));

        if (substr($sMethod,0,3) === static::METHOD_PREFIX) {
            if ((new \ReflectionClass($this))->hasMethod($sMethod)) {
                return $this->aMethods[$sMethod][$sHash] = (new \ReflectionMethod($this, $sMethod))->invokeArgs(new $sCalledClass, $aArgs);
            } else {
                // Cache is disabled or need a refresh for this method
                $sMethod = substr($sMethod, 3);
                return (new \ReflectionMethod($this, $sMethod))->invokeArgs(new $sCalledClass, $aArgs);
            }
        }

        $sCachedMethod = static::METHOD_PREFIX . $sMethod;
        if ((new \ReflectionClass($this))->hasMethod($sCachedMethod)) {
            if (@is_array($this->aMethods[$sCachedMethod]) || !@array_key_exists($sHash, $this->aMethods[$sCachedMethod])) {
                $this->aMethods[$sCachedMethod][$sHash] = (new \ReflectionMethod($this, $sCachedMethod))->invokeArgs(new $sCalledClass, $aArgs);
            }
            return $this->aMethods[$sCachedMethod][$sHash];
        }
    }
}
