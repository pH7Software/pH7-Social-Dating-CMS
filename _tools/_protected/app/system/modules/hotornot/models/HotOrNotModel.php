<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / HotOrNot / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;

class HotOrNotModel extends UserCoreModel
{
    /**
     * Get random picture.
     *
     * @param int|null $iProfileId
     * If the user is logged in, you need to specify its user ID, in order to not display its own profile since the user cannot vote for themselves.
     *
     * @param int $iApproved
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return \stdClass DATA ot the user (profileId, username, firstName, sex, avatar).
     */
    public function getPicture($iProfileId = null, $iApproved = 1, $iOffset = 0, $iLimit = 1)
    {
        $sSql = !empty($iProfileId) ? ' AND (profileId <> :profileId) AND FIND_IN_SET(sex, :matchSex)' : ' ';
        $rStmt = Db::getInstance()->prepare('SELECT profileId, username, firstName, sex, avatar FROM' . Db::prefix(DbTableName::MEMBER) .
            'WHERE (username <> :ghostUsername) AND (ban = 0)' . $sSql . ' AND (avatar IS NOT NULL) AND (approvedAvatar = :approved) ORDER BY RAND() LIMIT :offset, :limit');

        $rStmt->bindValue(':ghostUsername', PH7_GHOST_USERNAME, \PDO::PARAM_STR);

        if (!empty($iProfileId)) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
            $rStmt->bindValue(':matchSex', $this->getMatchSex($iProfileId), PDO::PARAM_STR);
        }
        $rStmt->bindValue(':approved', $iApproved, PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        $oRow = $rStmt->fetch(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $oRow;
    }
}
