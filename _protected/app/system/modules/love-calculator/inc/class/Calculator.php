<?php
/**
 * @title          Calculator Class
 * @desc           Calculates the amount of mutual attraction between the names of two members.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Love Calculator / Inc / Class
 * @version        1.2
 */

namespace PH7;

class Calculator
{
    /** @var string */
    private $sName;

    /** @var string */
    private $iStat;

    /**
     * @param string $sName
     * @param string $sSecondName
     */
    public function __construct($sName, $sSecondName)
    {
        $this->sName = strtolower(escape($sName . $sSecondName));
        $aName = count_chars($this->sName);

        for ($i = 97; $i <= 122; $i++) {
            if ($aName[$i] != false) {
                $iName2 = strlen($aName[$i]);
                if ($iName2 < 2) {
                    $aCalc[] = $aName[$i];
                } else {
                    for ($iA = 0; $iA < $iName2; $iA++) {
                        $aCalc[] = substr($aName[$i], $iA, 1);
                    }
                }
            }
        }

        while (($iLetter = count($aCalc)) > 2) {
            $iCenterLetter = ceil($iLetter / 2);
            for ($i = 0; $i < $iCenterLetter; $i++) {
                $sSum = array_shift($aCalc) + array_shift($aCalc);
                $iD = strlen($sSum);
                if ($iD < 2) {
                    $aCalcMore[] = $sSum;
                } else {
                    for ($iA = 0; $iA < $iD; $iA++) {
                        $aCalcMore[] = substr($sSum, $iA, 1);
                    }
                }
            }

            $iC = count($aCalcMore);
            for ($iB = 0; $iB < $iC; $iB++) {
                $aCalc[] = $aCalcMore[$iB];
            }
            array_splice($aCalcMore, 0);
        }

        $this->iStat = $aCalc[0] . $aCalc[1];
    }

    /**
     * @return int Return the love amount.
     */
    public function get()
    {
        return $this->iStat;
    }
}
