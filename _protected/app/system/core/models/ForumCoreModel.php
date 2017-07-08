<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class ForumCoreModel extends Framework\Mvc\Model\Engine\Model
{
    const
    CACHE_GROUP = 'db/sys/mod/forum',
    CREATED = 'createdDate DESC',
    UPDATED = 'updatedDate DESC',
    NAME = 'name ASC';

    /**
     * @param integer|null $iForumId
     * @param integer|null $iOffset
     * @param integer|null $iLimit
     * @param string $sOrder
     *
     * @return \stdClass|false
     */
    public function getForum($iForumId = null, $iOffset = null, $iLimit = null, $sOrder = self::NAME)
    {
        $bIsLimit = isset($iOffset, $iLimit);

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlLimit = ($bIsLimit) ? ' LIMIT :offset, :limit' : '';
        $sSqlForumId = (!empty($iForumId)) ? 'WHERE forumId = :forumId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Forums') . $sSqlForumId . 'ORDER BY ' . $sOrder . $sSqlLimit);

        if (!empty($iForumId)) {
            $rStmt->bindParam(':forumId', $iForumId, \PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        }

        if ($bIsLimit) {
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!empty($iForumId)) {
            return $rStmt->fetch(\PDO::FETCH_OBJ);
        } else {
            return $rStmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }

    /**
     * @param integer $iTopicId
     * @param integer|null $iMessageId
     * @param integer|null $iProfileId
     * @param integer $iApproved
     * @param integer $iOffset
     * @param integer $iLimit
     * @param string $sSort
     *
     * @return \stdClass|false
     */
    public function getMessage($iTopicId, $iMessageId = null, $iProfileId = null, $iApproved, $iOffset, $iLimit, $sSort = Db::ASC)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlMessageId = (!empty($iMessageId)) ? ' AND msg.messageId = :messageId ' : '';
        $sSqlProfileId = (!empty($iProfileId)) ? ' AND msg.profileId = :profileId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT f.name, t.title, t.forumId, msg.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Forums') .
            'AS f INNER JOIN' . Db::prefix('ForumsTopics') . 'AS t ON f.forumId = t.forumId INNER JOIN ' . Db::prefix('ForumsMessages') .
            'AS msg ON t.topicId = msg.topicId LEFT JOIN' . Db::prefix('Members') . 'AS m ON msg.profileId = m.profileId WHERE msg.topicId = :topicId ' .
            $sSqlMessageId . $sSqlProfileId . ' AND msg.approved = :approved ORDER BY msg.createdDate ' . $sSort . ' LIMIT :offset, :limit');

        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);

        if (!empty($iMessageId)) {
            $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        }

        if (!empty($iProfileId)) {
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        }

        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();

        if (!empty($iProfileId)) {
            return $rStmt->fetch(\PDO::FETCH_OBJ);
        } else {
            return $rStmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
}
