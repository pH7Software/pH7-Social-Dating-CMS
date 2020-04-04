<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class TwoFactorAuthCoreModel extends Framework\Mvc\Model\Engine\Model
{
    /** @var string */
    protected $sTable;

    /**
     * @param string $sMod
     */
    public function __construct($sMod)
    {
        parent::__construct();

        $this->sTable = Various::convertModToTable($sMod);
    }

    /**
     * @param int $iProfileId
     *
     * @return bool
     */
    public function isEnabled($iProfileId)
    {
        $sSql = 'SELECT isTwoFactorAuth FROM' . Db::prefix($this->sTable) . 'WHERE profileId = :profileId AND isTwoFactorAuth = \'1\' LIMIT 1';
        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchColumn() == 1;
    }
}
