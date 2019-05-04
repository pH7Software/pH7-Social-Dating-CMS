<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Module\Various as SysMod;

class Field
{
    const MEMBER_UNMODIFIABLE_FIELDS = [
        'profileid',
        'description',
        'punchline',
        'city',
        'state',
        'zipcode',
        'country'
    ];

    const AFFILIATE_UNMODIFIABLE_FIELDS = [
        'profileid',
        'description',
        'address',
        'phone',
        'city',
        'state',
        'zipcode',
        'country',
        'website'
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
     * @param string $sMod Mod name ("user" or "aff").
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
     * @param string $sMod Mod name ("user" or "aff").
     * @param string $sField Field name.
     *
     * @return bool
     */
    public static function doesExist($sMod, $sField)
    {
        $aFields = (new FieldModel(static::getTable($sMod)))->all();

        return in_array(
            strtolower($sField),
            array_map('strtolower', $aFields),
            true
        );
    }

    /**
     * Checks if the field is editable.
     *
     * @param string $sMod Mod name ("user" or "aff").
     * @param string $sField
     *
     * @return bool
     */
    public static function unmodifiable($sMod, $sField)
    {
        $aMemberUnmodifiableFields = static::MEMBER_UNMODIFIABLE_FIELDS;

        if (SysMod::isEnabled('sms-verification')) {
            $aMemberUnmodifiableFields[] = 'phone';
        }

        $aFields = $sMod === 'aff' ? static::AFFILIATE_UNMODIFIABLE_FIELDS : $aMemberUnmodifiableFields;

        return in_array(strtolower($sField), $aFields, true);
    }

    /**
     * Clean UserCoreModel cache.
     *
     * @return void
     */
    public static function clearCache()
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
