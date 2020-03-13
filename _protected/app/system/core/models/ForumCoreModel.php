<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use stdClass;

class ForumCoreModel extends Model
{
    const CACHE_GROUP = 'db/sys/mod/forum';
    const CREATED = 'createdDate DESC';
    const UPDATED = 'updatedDate DESC';
    const NAME = 'name ASC';

    /**
     * @param int|null $iForumId
     * @param int|null $iOffset
     * @param int|null $iLimit
     * @param string $sOrder
     *
     * @return array|stdClass|false
     */
    public function getForum($iForumId = null, $iOffset = null, $iLimit = null, $sOrder = self::NAME)
    {
        $bIsLimit = isset($iOffset, $iLimit);

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $bIsForumId = $iForumId !== null;

        $sSqlLimit = $bIsLimit ? ' LIMIT :offset, :limit' : '';
        $sSqlForumId = $bIsForumId ? 'WHERE forumId = :forumId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix(DbTableName::FORUM) . $sSqlForumId . 'ORDER BY ' . $sOrder . $sSqlLimit);

        if ($bIsForumId) {
            $rStmt->bindParam(':forumId', $iForumId, PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if ($bIsForumId) {
            return $rStmt->fetch(PDO::FETCH_OBJ);
        }

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int $iTopicId
     * @param int|null $iMessageId
     * @param int|null $iProfileId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     * @param string $sSort
     *
     * @return array|stdClass|false
     */
    public function getMessage($iTopicId, $iMessageId = null, $iProfileId = null, $sApproved, $iOffset, $iLimit, $sSort = Db::ASC)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $bIsMessageId = $iMessageId !== null;
        $bIsProfileId = $iProfileId !== null;

        $sSqlMessageId = $bIsMessageId ? ' AND msg.messageId = :messageId ' : '';
        $sSqlProfileId = $bIsProfileId ? ' AND msg.profileId = :profileId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT f.name, t.title, t.forumId, msg.*, m.username, m.firstName, m.sex FROM' . Db::prefix(DbTableName::FORUM) .
            'AS f INNER JOIN' . Db::prefix(DbTableName::FORUM_TOPIC) . 'AS t ON f.forumId = t.forumId INNER JOIN ' . Db::prefix(DbTableName::FORUM_MESSAGE) .
            'AS msg ON t.topicId = msg.topicId LEFT JOIN' . Db::prefix(DbTableName::MEMBER) . 'AS m ON msg.profileId = m.profileId WHERE msg.topicId = :topicId ' .
            $sSqlMessageId . $sSqlProfileId . ' AND msg.approved = :approved ORDER BY msg.createdDate ' . $sSort . ' LIMIT :offset, :limit');

        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);

        if ($bIsMessageId) {
            $rStmt->bindValue(':messageId', $iMessageId, PDO::PARAM_INT);
        }

        if ($bIsProfileId) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        }

        $rStmt->bindValue(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        if ($bIsProfileId) {
            return $rStmt->fetch(PDO::FETCH_OBJ);
        }

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }
}
