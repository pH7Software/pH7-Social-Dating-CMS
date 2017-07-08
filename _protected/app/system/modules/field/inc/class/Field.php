<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Inc / Class
 */

namespace PH7;

class Field
{
    /**
     * @desc Block constructing.
     * @access private
     */
    private function __construct() {}

    /**
     * Get table.
     *
     * @param string $sMod
     *
     * @return string
     */
    public static function getTable($sMod)
    {
        return (strtolower($sMod) == 'aff' ? 'Affiliates' : 'Members') . 'Info';
    }

    /**
     * Checks if the field exists.
     *
     * @param string $sMod Mod name.
     * @param string $sField Field name.
     *
     * @return boolean
     */
    public static function isExists($sMod, $sField)
    {
        $aFields = (new FieldModel(static::getTable($sMod)))->all();
        return in_array(strtolower($sField), array_map('strtolower', $aFields));
    }

    /**
     * Checks if the field is editable.
     *
     * @param string $sField
     *
     * @return boolean
     */
    public static function unmodifiable($sField)
    {
        $aList = ['profileid', 'middlename', 'description', 'businessname', 'address', 'street', 'city', 'state', 'zipcode', 'country', 'phone', 'fax', 'website', 'socialnetworksite', 'height', 'weight'];

        return in_array(strtolower($sField), $aList);

    }
}
