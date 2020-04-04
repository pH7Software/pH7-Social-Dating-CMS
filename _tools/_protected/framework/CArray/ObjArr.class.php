<?php
/**
 * @title            Object Array Class
 * @desc             Convert Objects and Arrays.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / CArray
 */

namespace PH7\Framework\CArray;

defined('PH7') or exit('Restricted access');

use stdClass;

class ObjArr
{
    /**
     * Converting an Array to an Object.
     *
     * @param mixed $aArr The array to convert.
     *
     * @return stdClass
     */
    public static function toObject($mArr)
    {
        $oData = new stdClass;

        if (is_array($mArr)) {
            foreach ($mArr as $sKey => $mVal) {
                if (is_array($mVal)) {
                    $oData->$sKey = self::toObject($mVal); // Recursive method
                } else {
                    $oData->$sKey = $mVal;
                }
            }
        }

        return $oData;
    }

    /**
     * Converting an Object to an Array.
     *
     * @param stdClass|array $oObj The object to convert.
     *
     * @return array
     */
    public static function toArray($oObj)
    {
        if (is_array($oObj) || is_object($oObj)) {
            $aRes = [];
            foreach ($oObj as $sKey => $sVal) {
                $aRes[$sKey] = self::toArray($sVal); // Recursive method
            }
            return $aRes;
        }

        return $oObj;
    }
}
