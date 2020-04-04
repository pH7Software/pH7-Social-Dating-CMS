<?php
/**
 * @title            Form Class
 * @desc             Some useful form methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class Form extends Framework\Layout\Form\Form
{
    use Framework\Layout\Form\Message;

    const MIN_STRING_FIELD_LENGTH = 2;
    const MAX_STRING_FIELD_LENGTH = 200;

    /**
     * To get Value Data from the database.
     *
     * @param string $sValue
     *
     * @return array
     */
    public static function getVal($sValue)
    {
        $aVal = [];
        $aValue = explode(Db::SET_DELIMITER, $sValue);

        foreach ($aValue as $sVal) {
            $aVal[] = $sVal;
        }

        return $aVal;
    }

    /**
     * To set Value Data into the database.
     *
     * @param array $aValue
     *
     * @return string
     */
    public static function setVal($aValue)
    {
        $sVal = ''; // Default Value

        foreach ($aValue as $sValue) {
            $sVal .= $sValue . Db::SET_DELIMITER;
        }

        return rtrim($sVal, Db::SET_DELIMITER); // Removes the MySQL SET's delimiter
    }

    /**
     * @param string $sTable The DB country table name.
     *
     * @return array
     */
    public static function getCountryValues($sTable = DbTableName::MEMBER_COUNTRY)
    {
        $aSelectedCountries = [];

        $aCountries = (new UserCoreModel)->getCountries($sTable);
        foreach ($aCountries as $oCountry) {
            $aSelectedCountries[$oCountry->countryCode] = t($oCountry->countryCode); // Translate country ID
        }

        return $aSelectedCountries;
    }

    /**
     * Prevent against brute-force attack to avoid drowning the server and database.
     *
     * @param int $iDelayInSec Delay in seconds.
     *
     * @return void
     */
    protected function preventBruteForce($iDelayInSec)
    {
        sleep($iDelayInSec);
    }
}
