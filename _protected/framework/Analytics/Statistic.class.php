<?php
/**
 * @title            Statistic Class
 * @desc             View Statistics methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Analytics
 * @version          1.1
 */

namespace PH7\Framework\Analytics;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc;

class Statistic
{

    /**
     * Private constructor to prevent instantiation of class since it is a static class.
     *
     * @access private
     */
    private function __construct() {}


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
