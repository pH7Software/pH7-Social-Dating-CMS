<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Inc / Class
 */

namespace PH7;

class Field
{
    const UNMODIFIABLE_FIELDS = [
        'profileid',
        'middlename',
        'description',
        'businessname',
        'address',
        'street',
        'city',
        'state',
        'zipcode',
        'country',
        'phone',
        'fax',
        'website',
        'socialnetworksite',
        'height',
        'weight'
    ];

    /**
     * Block constructing.
     */
    private function __construct()
    {
    }

    /**
     * Get table.
     *
     * @param string $sMod
     *
     * @return string
     */
    public static function getTable($sMod)
    {
        return (strtolower($sMod) === 'aff' ? DbTableName::AFFILIATE : DbTableName::MEMBER) . '_info';
    }

    /**
     * Checks if the field exists.
     *
     * @param string $sMod Mod name.
     * @param string $sField Field name.
     *
     * @return bool
     */
    public static function isExists($sMod, $sField)
    {
        $aFields = (new FieldModel(static::getTable($sMod)))->all();

        return in_array(strtolower($sField), array_map('strtolower', $aFields), true);
    }

    /**
     * Checks if the field is editable.
     *
     * @param string $sField
     *
     * @return bool
     */
    public static function unmodifiable($sField)
    {
        return in_array(strtolower($sField), static::UNMODIFIABLE_FIELDS, true);

    }
}
