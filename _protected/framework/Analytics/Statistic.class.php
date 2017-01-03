<?php
/**
 * @title            Statistic Class
 * @desc             View Statistics methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Analytics
 * @version          1.1
 */

namespace PH7\Framework\Analytics;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc, PH7\Framework\Pattern\Statik;

class Statistic
{
    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;


    /**
     * Set Views (pH Views) Statistics with a verification session to avoid duplication in the number of page views.
     *
     * @static
     * @param integer $iId
     * @param string $sTable
     * @return void
     */
    public static function setView($iId, $sTable)
    {
        $oSession = new \PH7\Framework\Session\Session;
        $sSessionName = 'pHV' . $iId . $sTable;

        if (!$oSession->exists($sSessionName))
        {
            Mvc\Model\Statistic::setView($iId, $sTable);
            $oSession->set($sSessionName, 1);
        }

        unset($oSession);
    }
}
