<?php
/**
 * @title          Height Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Math / Measure
 * @version        1.0
 */

namespace PH7\Framework\Math\Measure;

defined('PH7') or exit('Restricted access');

class Height extends Measure implements Measurable
{
    /**
     * Converts a height value given in centimeters to feet / inches.
     *
     * @return array ['cm', 'ft', 'in'] Give: Centimeters / Feet / Inches
     */
    public function get()
    {
        $iInch = round($this->iUnit * 0.393700787);
        $iFoot = floor($iInch / 12);
        $iInch = ($iInch % 12);

        return [
            'cm' => $this->iUnit,
            'ft' => $iFoot,
            'in' => $iInch
        ];
    }

    /**
     * Display height (centimeters, feet and inches).
     *
     * @see self::get()
     *
     * @param bool $bPrint
     *
     * @return void|string
     */
    public function display($bPrint = false)
    {
        $aData = $this->get();
        $sHeightTxt = t('%0% ′ %1% ″ – %2% cm', $aData['ft'], $aData['in'], $aData['cm']);

        if (!$bPrint) {
            return $sHeightTxt;
        }

        echo $sHeightTxt;
    }
}
