<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class ForumCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const
    CACHE_GROUP = 'db/sys/mod/forum',
    CREATED = 'f.createdDate DESC',
    UPDATED = 'f.updatedDate DESC',
    NAME = 'c.title, f.name ASC';

    public function getForum($iForumId = null, $iOffset, $iLimit, $sOrder = self::NAME)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;
        $sSqlForumId = (isset($iForumId)) ? 'WHERE forumId =:forumId' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('ForumsCategories') . 'AS c INNER JOIN' .Db::prefix('Forums') . 'AS f ON c.categoryId = f.categoryId ' .  $sSqlForumId  . ' ORDER BY ' . $sOrder . ' LIMIT :offset, :limit');
        (isset($iForumId)) ? $rStmt->bindParam(':forumId', $iForumId, \PDO::PARAM_INT) : '';
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return (isset($iForumId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getMessage($iTopicId, $iMessageId = null, $iProfileId = null, $iApproved, $iOffset, $iLimit, $sSort = Db::ASC)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlMessageId = (isset($iMessageId)) ? ' AND msg.messageId =:messageId ' : '';
        $sSqlProfileId = (isset($iProfileId)) ? ' AND msg.profileId =:profileId ' : '';
        $rStmt = Db::getInstance()->prepare('SELECT f.name, t.title, t.forumId, msg.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Forums') . 'AS f INNER JOIN' . Db::prefix('ForumsTopics') . 'AS t ON f.forumId = t.forumId INNER JOIN ' . Db::prefix('ForumsMessages') . 'AS msg ON t.topicId = msg.topicId LEFT JOIN' . Db::prefix('Members')  . 'AS m
        ON msg.profileId = m.profileId WHERE msg.topicId =:topicId ' . $sSqlMessageId . $sSqlProfileId . ' AND msg.approved=:approved ORDER BY msg.createdDate ' . $sSort . ' LIMIT :offset, :limit');
        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        if(isset($iMessageId)) $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        if(isset($iProfileId)) $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return (isset($iProfileId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

}
