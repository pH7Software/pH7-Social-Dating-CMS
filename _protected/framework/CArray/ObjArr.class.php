<?php
/**
 * @title            Object Array Class
 * @desc             Convert Objects and Arrays.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / CArray
 * @version          1.0
 */

namespace PH7\Framework\CArray;
defined('PH7') or exit('Restricted access');

class ObjArr
{

     /**
      * Converting an Array to an Object.
      *
      * @static
      * @param array $aArr The array to convert.
      * @return object
      */
    public static function toObject(array $aArr)
    {
        $oData = new \stdClass;
        if (is_array($aArr))
        {
            foreach ($aArr as $sKey => $mVal)
            {
                if (is_array($mVal))
                    $oData->$sKey = self::toObject($mVal); // Recursive method
                else
                    $oData->$sKey = $mVal;
            }
        }
        return $oData;
    }

    /**
     * Converting an Object to an Array.
     *
     * @static
     * @param object $oObj The object to convert.
     * @return array
     */
    public static function toArray($oObj)
    {
        if (is_array($oObj) || is_object($oObj))
        {
            $aRes = array();
            foreach ($oObj as $sKey => $sVal)
                $aRes[$sKey] = self::toArray($sVal); // Recursive method
            return $aRes;
        }
        return $oObj;
    }

}
