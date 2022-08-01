<?php
/**
 * @title            Array Class
 * @desc             Useful methods for the handing Array.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / CArray
 */

declare(strict_types=1);

namespace PH7\Framework\CArray;

defined('PH7') or exit('Restricted access');

class CArray
{
    /**
     * Merge two Arrays into one recursively.
     *
     * @param array $aFrom The array to be merged to.
     * @param array $aTo The array to be merged from.
     *
     * @return array Returns the merged array (the original arrays are not changed).
     */
    public static function merge(array $aFrom, array $aTo): array
    {
        foreach ($aTo as $mKey => $mVal) {
            if (is_int($mKey)) {
                $aFrom[] = $mVal;
            } elseif (is_array($mVal) && isset($aFrom[$mKey]) && is_array($aFrom[$mKey])) {
                $aFrom[$mKey] = self::merge($aFrom[$mKey], $mVal); // Recursive method
            } else {
                $aFrom[$mKey] = $mVal;
            }
        }

        return $aFrom;
    }

    /**
     * Get Key in Array By Array Value.
     *
     * @param string $sValue The value in the array.
     * @param array $aArray The array.
     *
     * @return string|null The name key. If the key is not found, returns NULL
     */
    public static function getKeyByValue($sValue, array $aArray): ?string
    {
        $mKey = array_search($sValue, $aArray, true);

        return static::get($mKey);
    }

    /**
     * Get Key in Array By Array Value without case sensitive.
     *
     * @param string $sValue The value in the array.
     * @param array $aArray The array.
     *
     * @return string|null The name key. If the key is not found, returns NULL
     */
    public static function getKeyByValueIgnoreCase($sValue, array $aArray): ?string
    {
        $mKey = array_search(strtolower($sValue), array_map('strtolower', $aArray), true);

        return static::get($mKey);
    }

    /**
     * Get Value in Array By Array Key
     *
     * @param string $sKey The key in the array.
     * @param array $aArray The array.
     *
     * @return string|null The value of the array. If the value is not found, returns NULL
     */
    public static function getValueByKey(string $sKey, array $aArray): ?string
    {
        return array_key_exists($sKey, $aArray) && !empty($aArray[$sKey]) ? $aArray[$sKey] : null;
    }

    /**
     * Check if the key exists.
     *
     * @param string|bool $mKey The key for needle if it is found in the array, FALSE otherwise.
     *
     * @return string|null The name key. If the key is not found, returns NULL
     */
    private static function get($mKey): ?string
    {
        return $mKey !== false ? $mKey : null;
    }
}
