<?php
/**
 * @title          Weight Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;
defined('PH7') or exit('Restricted access');

class Weight extends Measure implements IMeasure
{
    /**
     * Converts a weight value given in kilograms to stones / pounds.
     *
     * @return array ['kg', 'st', 'lb'] Give: Kilograms / Stones / Pounds
     */
    public function get()
    {
        $iStone = round($this->iUnit * 0.157473);
        $iPound = round($this->iUnit * 2.20462);
        return ['kg' => $this->iUnit, 'st' => $iStone, 'lb' => $iPound];
    }

    /**
     * Display weight (kilograms, stones and pounds).
     *
     * @see get()
     *
     * @param boolean $bPrint Default FALSE
     * @return void
     */
    public function display($bPrint = false)
    {
        $aData = $this->get();
        $sWeightTxt = t('%0% sts &ndash; %1% lbs &ndash; %2% kgs', $aData['st'], $aData['lb'], $aData['kg']);

        if($bPrint)
            echo $sWeightTxt;
        else
            return $sWeightTxt;
    }
}
