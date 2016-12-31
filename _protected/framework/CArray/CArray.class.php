<?php
/**
 * @title            Array Class
 * @desc             Useful methods for the handing Array.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / CArray
 * @version          1.0
 */

namespace PH7\Framework\CArray;
defined('PH7') or exit('Restricted access');

class CArray
{

    /**
     * Merge two Arrays into one recursively.
     *
     * @access public
     * @param array $aFrom The array to be merged to.
     * @param array $aTo The array to be merged from.
     * @return array Returns the merged array (the original arrays are not changed).
     */
    public static function merge(array $aFrom, array $aTo)
    {
        foreach ($aTo as $mKey => $mVal)
        {
            if (is_int($mKey))
                $aFrom[] = $mVal;
            elseif (is_array($mVal) && isset($aFrom[$mKey]) && is_array($aFrom[$mKey]))
                $aFrom[$mKey] = self::merge($aFrom[$mKey], $mVal); // Recursive method
            else
                $aFrom[$mKey] = $mVal;
        }
        return $aFrom;
    }

    /**
     * Get Key in Array By Array Value.
     *
     * @access public
     * @param string $sValue The value in the array.
     * @param array $aArray The array.
     * @return string The name key. If the key is not found, Returns NULL.
     */
    public static function getKeByVal($sValue, array $aArray)
    {
        $mKey = array_search($sValue, $aArray);
        return static::_get($mKey);
    }

    /**
     * Get Key in Array By Array Value without case sensitive.
     *
     * @access public
     * @param string $sValue The value in the array.
     * @param array $aArray The array.
     * @return string The name key. If the key is not found, Returns NULL.
     */
    public static function getKeyByValIgnoreCase($sValue, array $aArray)
    {
        $mKey = array_search(strtolower($sValue), array_map('strtolower', $aArray));
        return static::_get($mKey);
    }

    /**
     * Get Value in Array By Array Key
     *
     * @access public
     * @param string $sKey The key in the array.
     * @param array $aArray The array.
     * @return string The value of the array. If the value is not found, Returns NULL.
     */
    public static function getValueByKey($sKey, array $aArray)
    {
        return (array_key_exists($sKey, $aArray) && !empty($aArray[$sKey])) ? $aArray[$sKey] : null;
    }

    /**
     * Check if the key exists.
     *
     * @access private
     * @param mixed (string | boolean) $mKey The key for needle if it is found in the array, FALSE otherwise.
     * @return string The name key. If the key is not found, Returns NULL.
     */
    private static function _get($mKey)
    {
        return ($mKey !== false) ? $mKey : null;
    }

}
