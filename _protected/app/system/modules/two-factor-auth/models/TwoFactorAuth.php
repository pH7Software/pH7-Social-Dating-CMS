<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Model
 */

namespace PH7;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class TwoFactorAuth extends Framework\Mvc\Model\Engine\Model
{
    private $sTable;

    public function __construct($sMod)
    {
        $this->sTable = Various::convertModToTable($sMod);
    }

    public function isEnabled($iProfileId, $sMod)
    {
        $rStmt = Db::getInstance()->prepare('SELECT isTwoFactorAuth FROM' . Db::prefix($this->sTable) . 'WHERE profileId = :profileId AND isTwoFactorAuth = \'1\' LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        return ($rStmt->fetchColumn() == 1);
    }

    public function setStatus($iIsEnabled)
    {
        $this->orm->update($this->sTable, 'isTwoFactorAuth', $iIsEnabled);
    }
}
