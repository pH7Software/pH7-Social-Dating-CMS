<?php
/**
 * @title            Form Class
 * @desc             Some useful form methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

class Form extends Framework\Layout\Form\Form
{
    /**
     * To get Value Data from the database.
     *
     * @param string $sValue
     * @return array
     */
    public static function getVal($sValue)
    {
        $aVal = array(); // Default Value
        $aValue = explode(',', $sValue);

        foreach ($aValue as $sVal)
            $aVal[] = $sVal;

        return $aVal;
    }

    /**
     * To set Value Data into the database.
     *
     * @param array $aValue
     * @return string
     */
    public static function setVal($aValue)
    {
        $sVal = ''; // Devault Value

        foreach ($aValue as $sValue)
            $sVal .= $sValue . ',';

        return rtrim($sVal, ','); // Removes the last comma
    }
}
