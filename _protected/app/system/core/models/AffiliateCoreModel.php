<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

// Abstract Class
class AffiliateCoreModel extends AdminCoreModel
{

    /**
     * Delete Affiliate.
     *
     * @param integer $iProfileId
     * @param string $sUsername
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int) $iProfileId;

        $oDb = Db::getInstance();
        $oDb->exec('DELETE FROM' . Db::prefix('AffiliateInfo') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        $oDb->exec('DELETE FROM' . Db::prefix('Affiliate') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        unset($oDb);
    }

}
