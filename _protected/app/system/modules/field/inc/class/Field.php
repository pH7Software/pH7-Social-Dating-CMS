<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013, Pierre-Henry Soria. All Rights Reserved.
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
     * @return string
     */
    public static function getTable($sMod)
    {
        return (strtolower($sMod) == 'aff' ? 'Affiliate' : 'Members') . 'Info';
    }

    /**
     * Check table.
     *
     * @param string $sTable
     * @return mixed (string or void) Returns the table if it is correct.
     * @see \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr()
     * @throws If the table is not valid, a message is displayed with the method \PH7\Framework\Mvc\Model\Engine\Util\Various::launchErr() and exit().
     */
    public static function checkTable($sTable)
    {
        if ($sTable != 'MembersInfo' && $sTable != 'AffiliateInfo')
            Framework\Mvc\Model\Engine\Util\Various::launchErr($sTable);
        else
            return $sTable;
    }

    /**
     * Prevents duplication of field in a table.
     *
     * @param string $sMod Mod name.
     * @param string $sField Field name.
     * @return boolean
     */
    public static function isDuplicate($sMod, $sField)
    {
        $aFields = (new FieldModel(static::getTable($sMod)))->all();
        return in_array(strtolower($sField), array_map('strtolower', $aFields));
    }

    /**
     * Checks if the field is editable.
     *
     * @param string $sField
     * @return boolean
     */
    public static function unmodifiable($sField)
    {
        $aList = ['profileId', 'middleName', 'description', 'businessName', 'address', 'street', 'city', 'state', 'zipCode', 'country', 'phone', 'fax', 'website', 'socialNetworkSite', 'height', 'weight'];
        return in_array(strtolower($sField), array_map('strtolower', $aList));

    }

}
