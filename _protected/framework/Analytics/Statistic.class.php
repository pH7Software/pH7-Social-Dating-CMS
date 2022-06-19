<?php
/**
 *                   View Statistics methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Analytics
 */

declare(strict_types=1);

namespace PH7\Framework\Analytics;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Statistic as StatisticModel;
use PH7\Framework\Pattern\Statik;
use PH7\Framework\Session\Session;

class Statistic
{
    /** pHV = "pH Views" */
    private const SESSION_PREFIX = 'pHV';

    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;

    /**
     * Set Views (pH Views) Statistics with a verification session to avoid duplication in the number of page views.
     *
     * @param int $iId
     * @param string $sTable
     */
    public static function setView($iId, string $sTable): void
    {
        $oSession = new Session;
        $sSessionName = static::SESSION_PREFIX . $iId . $sTable;

        if (!$oSession->exists($sSessionName)) {
            StatisticModel::setView($iId, $sTable);
            $oSession->set($sSessionName, 1);
        }

        unset($oSession);
    }
}
