<?php
/**
 * @title          Calculator Class
 * @desc           Calculates the amount of mutual attraction between the names of two members.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Love Calculator / Inc / Class
 * @version        1.2
 */

namespace PH7;

class Calculator
{
    /** @var string */
    private $iStat;

    /**
     * @param string $sName
     * @param string $sSecondName
     */
    public function __construct($sName, $sSecondName)
    {
        $aCalculation = $aCalculationMore = [];
        $sName = strtolower(escape($sName . $sSecondName));
        $aName = count_chars($sName);

        for ($iIndex = 97; $iIndex <= 122; $iIndex++) {
            if ($aName[$iIndex] != false) {
                $iName2 = strlen($aName[$iIndex]);
                if ($iName2 < 2) {
                    $aCalculation[] = $aName[$iIndex];
                } else {
                    for ($iA = 0; $iA < $iName2; $iA++) {
                        $aCalculation[] = substr($aName[$iIndex], $iA, 1);
                    }
                }
            }
        }

        while (($iLetter = count($aCalculation)) > 2) {
            $iCenterLetter = ceil($iLetter / 2);
            for ($iQuantity = 0; $iQuantity < $iCenterLetter; $iQuantity++) {
                $sSum = array_shift($aCalculation) + array_shift($aCalculation);
                $iD = strlen($sSum);
                if ($iD < 2) {
                    $aCalculationMore[] = $sSum;
                } else {
                    for ($iA = 0; $iA < $iD; $iA++) {
                        $aCalculationMore[] = substr($sSum, $iA, 1);
                    }
                }
            }

            $iC = count($aCalculationMore);
            for ($iB = 0; $iB < $iC; $iB++) {
                $aCalculation[] = $aCalculationMore[$iB];
            }
            array_splice($aCalculationMore, 0);
        }

        $this->iStat = $aCalculation[0] . $aCalculation[1];
    }

    /**
     * @return int Return the love amount.
     */
    public function get()
    {
        return $this->iStat;
    }
}
