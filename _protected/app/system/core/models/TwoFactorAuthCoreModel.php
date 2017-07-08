<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class TwoFactorAuthCoreModel extends Framework\Mvc\Model\Engine\Model
{
    protected $sTable;

    public function __construct($sMod)
    {
        parent::__construct();

        $this->sTable = Various::convertModToTable($sMod);
    }

    public function isEnabled($iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT isTwoFactorAuth FROM' . Db::prefix($this->sTable) . 'WHERE profileId = :profileId AND isTwoFactorAuth = \'1\' LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchColumn() == 1;
    }
}
