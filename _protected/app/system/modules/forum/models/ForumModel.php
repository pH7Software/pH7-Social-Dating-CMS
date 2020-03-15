<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Spam;
use stdClass;

class ForumModel extends ForumCoreModel
{
    /**
     * @param int|null $iCategoryId
     * @param int|null $iOffset
     * @param int|null $iLimit
     *
     * @return array|stdClass|bool
     */
    public function getCategory($iCategoryId = null, $iOffset = null, $iLimit = null)
    {
        $bIsLimit = isset($iOffset, $iLimit);

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $bIsCategoryId = $iCategoryId !== null;

        $sSqlLimit = $bIsLimit ? ' LIMIT :offset, :limit' : '';
        $sSqlCategoryId = $bIsCategoryId ? ' WHERE categoryId = :categoryId ' : '';

        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix(DbTableName::FORUM_CATEGORY) . $sSqlCategoryId . 'ORDER BY title ASC' . $sSqlLimit
        );
        if ($bIsCategoryId) {
            $rStmt->bindParam(':categoryId', $iCategoryId, PDO::PARAM_INT);
        }
        if ($bIsLimit) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        }
        if ($bIsLimit) {
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }
        $rStmt->execute();

        return $bIsCategoryId ? $rStmt->fetch(PDO::FETCH_OBJ) : $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sForumName
     * @param int $iForumId
     * @param string $sTopicSubject
     * @param int $iTopicId
     * @param int $iProfileId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array|stdClass|bool
     */
    public function getTopic($sForumName, $iForumId, $sTopicSubject, $iTopicId, $iProfileId, $sApproved, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $bIsProfileId = $iProfileId !== null;

        $sSqlProfileId = $bIsProfileId ? 'AND t.profileId = :profileId ' : '';
        $sSqlMsg = isset($sTopicSubject, $iTopicId) ? ' AND (t.title LIKE :topicSubject AND t.topicId = :topicId) ' : '';

        $rStmt = Db::getInstance()->prepare(
            'SELECT f.*, f.createdDate AS forumCreatedDate, f.updatedDate AS forumUpdatedDate, t.*, m.username, m.firstName, m.sex FROM' .
            Db::prefix(DbTableName::FORUM) . 'AS f INNER JOIN' .
            Db::prefix(DbTableName::FORUM_TOPIC) . 'AS t ON f.forumId = t.forumId LEFT JOIN' . Db::prefix(DbTableName::MEMBER) .
            ' AS m ON t.profileId = m.profileId WHERE (t.forumId = :forumId AND f.name LIKE :forumName) ' . $sSqlMsg . $sSqlProfileId .
            ' AND (t.approved = :approved) ORDER BY t.createdDate DESC LIMIT :offset, :limit'
        );

        $rStmt->bindValue(':forumName', $sForumName . '%', PDO::PARAM_STR);
        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);

        if (isset($sTopicSubject, $iTopicId)) {
            $rStmt->bindValue(':topicSubject', $sTopicSubject . '%', PDO::PARAM_STR);
            $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        }

        if ($bIsProfileId) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        }
        $rStmt->bindValue(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return isset($sTopicSubject, $iTopicId) ? $rStmt->fetch(PDO::FETCH_OBJ) : $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sTitle
     *
     * @return bool
     */
    public function addCategory($sTitle)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix(DbTableName::FORUM_CATEGORY) . '(title) VALUES(:title)');
        $rStmt->bindValue(':title', $sTitle, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iCategoryId
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sCreatedDate
     *
     * @return bool
     */
    public function addForum($iCategoryId, $sTitle, $sDescription, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix(DbTableName::FORUM) . '(categoryId, name, description, createdDate)
            VALUES(:categoryId, :title, :description, :createdDate)'
        );

        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iForumId
     * @param string $sTitle
     * @param string $sMessage
     * @param string $sCreatedDate
     *
     * @return bool
     */
    public function addTopic($iProfileId, $iForumId, $sTitle, $sMessage, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix(DbTableName::FORUM_TOPIC) . '(profileId, forumId, title, message, createdDate)
            VALUES(:profileId, :forumId, :title, :message, :createdDate)'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iTopicId
     * @param string $sMessage
     * @param string $sCreatedDate
     *
     * @return bool
     */
    public function addMessage($iProfileId, $iTopicId, $sMessage, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix(DbTableName::FORUM_MESSAGE) . '(profileId, topicId, message, createdDate)
            VALUES(:profileId, :topicId, :message, :createdDate)'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iCategoryId
     * @param string $sTitle
     *
     * @return bool
     */
    public function updateCategory($iCategoryId, $sTitle)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix(DbTableName::FORUM_CATEGORY) .
            'SET title = :title WHERE categoryId = :categoryId');

        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iForumId
     * @param int $iCategoryId
     * @param string $sName
     * @param string $sDescription
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updateForum($iForumId, $iCategoryId, $sName, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::FORUM) .
            'SET categoryId = :categoryId, name = :name, description = :description, updatedDate = :updatedDate WHERE forumId = :forumId'
        );

        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);
        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sName, PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iTopicId
     * @param string $sTitle
     * @param string $sMessage
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updateTopic($iProfileId, $iTopicId, $sTitle, $sMessage, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::FORUM_TOPIC) .
            'SET title = :title, message = :message, updatedDate = :updatedDate WHERE profileId = :profileId AND topicId = :topicId'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * @param int $iProfileId
     * @param int $iMessageId
     * @param string $sMessage
     * @param string $sUpdatedDate
     *
     * @return bool
     */
    public function updateMessage($iProfileId, $iMessageId, $sMessage, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::FORUM_MESSAGE) .
            'SET message = :message, updatedDate = :updatedDate WHERE profileId = :profileId AND messageId = :messageId'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, PDO::PARAM_INT);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Deletes the category and forums, topics and posts in it.
     *
     * @param int $iCategoryId
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function deleteCategory($iCategoryId)
    {
        // Topics of Forums & Messages of Topics
        $this->delMsgsTopicsFromCatId($iCategoryId);

        // Forums of Category
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM) . 'WHERE categoryId = :categoryId'
        );
        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);
        $rStmt->execute();

        // Category
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM_CATEGORY) . 'WHERE categoryId = :categoryId LIMIT 1'
        );
        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Deletes the forum and the topics and posts in it.
     *
     * @param int $iForumId
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function deleteForum($iForumId)
    {
        // Messages of Topics
        $this->delMsgsFromForumId($iForumId);

        // Topics of Forum
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . 'WHERE forumId = :forumId'
        );
        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);
        $rStmt->execute();

        // Forum
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM) . 'WHERE forumId = :forumId LIMIT 1'
        );
        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Deletes the topic and posts in it.
     *
     * @param int $iProfileId
     * @param int $iTopicId
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function deleteTopic($iProfileId, $iTopicId)
    {
        // Messages of Topic
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) . 'WHERE profileId = :profileId AND topicId = :topicId'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        $rStmt->execute();

        // Topic
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . 'WHERE profileId = :profileId AND topicId = :topicId LIMIT 1'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Deletes posts.
     *
     * @param int $iProfileId
     * @param int $iMessageId
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function deleteMessage($iProfileId, $iMessageId)
    {
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) .
            'WHERE profileId = :profileId AND messageId = :messageId LIMIT 1'
        );
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * Search Topics.
     *
     * @param int|string $mLooking (integer for Topic ID or string for a keyword)
     * @param bool $bCount Put 'true' for count the topics or 'false' for the result of topics.
     * @param string $sOrderBy
     * @param int $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|stdClass (integer for the number topics returned or an object for the topics list)
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);
        $bDigitSearch = ctype_digit($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 't');

        $sSqlLimit = !$bCount ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = !$bCount ? 'f.*, f.createdDate AS forumCreatedDate, f.updatedDate AS forumUpdatedDate, t.*, m.username, m.firstName, m.sex' : 'COUNT(t.topicId)';

        $sSqlWhere = ' WHERE t.message LIKE :looking OR t.title LIKE :looking OR m.username LIKE :looking';
        if ($bDigitSearch) {
            $sSqlWhere = ' WHERE t.topicId = :looking ';
        }

        $rStmt = Db::getInstance()->prepare(
            'SELECT ' . $sSqlSelect . ' FROM' . Db::prefix(DbTableName::FORUM) . 'AS f INNER JOIN' .
            Db::prefix(DbTableName::FORUM_TOPIC) . 'AS t ON f.forumId = t.forumId LEFT JOIN' .
            Db::prefix(DbTableName::MEMBER) . ' AS m ON t.profileId = m.profileId' . $sSqlWhere . $sSqlOrder . $sSqlLimit
        );

        if ($bDigitSearch) {
            $rStmt->bindValue(':looking', $mLooking, PDO::PARAM_INT);
        } else {
            $rStmt->bindValue(':looking', '%' . $mLooking . '%', PDO::PARAM_STR);
        }

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $mData = (int)$rStmt->fetchColumn();
        }

        Db::free($rStmt);

        return $mData;
    }

    /**
     * @param int $iProfileId
     * @param string $sApproved
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getPostByProfile($iProfileId, $sApproved, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix(DbTableName::FORUM) .
            ' AS f INNER JOIN ' . Db::prefix(DbTableName::FORUM_TOPIC) .
            'AS t ON f.forumId = t.forumId WHERE t.profileId = :profileId AND t.approved = :approved GROUP BY t.topicId ORDER BY t.createdDate DESC LIMIT :offset, :limit'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $sApproved, PDO::PARAM_STR);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param int|null $iProfileId
     *
     * @return int
     */
    public function totalForums($iProfileId = null)
    {
        $bIsProfileId = $iProfileId !== null;

        $sSqlProfileId = $bIsProfileId ? ' WHERE profileId = :profileId' : '';

        $rStmt = Db::getInstance()->prepare('SELECT COUNT(forumId) FROM' . Db::prefix(DbTableName::FORUM) . $sSqlProfileId);
        if ($bIsProfileId) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        }
        $rStmt->execute();
        $iTotalForums = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalForums;
    }

    /**
     * Get the total number of topic.
     *
     * @param int $iForumId Search by topic ID. Default NULL
     * @param int $iProfileId Search by user ID. Default NULL
     *
     * @return int
     */
    public function totalTopics($iForumId = null, $iProfileId = null)
    {
        $sSqlWhere = '';
        if ($iForumId !== null) {
            $sSqlWhere = ' WHERE forumId = :forumId';
        } elseif ($iProfileId !== null) {
            $sSqlWhere = ' WHERE profileId = :profileId';
        }

        $rStmt = Db::getInstance()->prepare(
            'SELECT COUNT(topicId) FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . $sSqlWhere
        );

        if ($iForumId !== null) {
            $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);
        } elseif ($iProfileId !== null) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        }

        $rStmt->execute();
        $iTotalTopics = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalTopics;
    }

    /**
     * @param int|null $iTopicId
     * @param int|null $iProfileId
     *
     * @return int
     */
    public function totalMessages($iTopicId = null, $iProfileId = null)
    {
        $sSqlWhere = '';
        if ($iTopicId !== null) {
            $sSqlWhere = ' WHERE topicId = :topicId';
        } elseif ($iProfileId !== null) {
            $sSqlWhere = ' WHERE profileId = :profileId';
        }

        $rStmt = Db::getInstance()->prepare(
            'SELECT COUNT(messageId) FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) . $sSqlWhere
        );

        if ($iTopicId !== null) {
            $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        } elseif ($iProfileId !== null) {
            $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        }

        $rStmt->execute();
        $iTotalMessages = (int)$rStmt->fetchColumn();
        Db::free($rStmt);

        return $iTotalMessages;
    }

    /**
     * Check Duplicate Topics.
     *
     * @param string $sCheckMsg
     * @param int $iProfileId
     *
     * @return bool Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateTopic($iProfileId, $sCheckMsg)
    {
        return Spam::detectDuplicate(
            $sCheckMsg,
            'message',
            'topicId',
            $iProfileId,
            DbTableName::FORUM_TOPIC
        );
    }

    /**
     * Check Duplicate Messages.
     *
     * @param string $sCheckMsg
     * @param int $iProfileId
     *
     * @return bool Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateMessage($iProfileId, $sCheckMsg)
    {
        return Spam::detectDuplicate(
            $sCheckMsg,
            'message',
            'messageId',
            $iProfileId,
            DbTableName::FORUM_MESSAGE
        );
    }

    /**
     * To prevent spam!
     * Waiting time to send a new topic in the forum.
     *
     * @param int $iProfileId
     * @param int $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     *
     * @return bool Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitTopic($iProfileId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT topicId FROM' . Db::prefix(DbTableName::FORUM_TOPIC) .
            'WHERE profileId = :profileId AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1'
        );

        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 0;
    }

    /**
     * To prevent spam!
     * Waiting time to send a reply message in the same topic.
     *
     * @param int $iTopicId
     * @param int $iProfileId
     * @param int $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     *
     * @return bool Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitReply($iTopicId, $iProfileId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT messageId FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) .
            'WHERE topicId = :topicId AND profileId = :profileId AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1'
        );

        $rStmt->bindValue(':topicId', $iTopicId, PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->rowCount() === 0;
    }

    /**
     * Get Topic IDs from Forum ID.
     *
     * @param int $iForumId
     *
     * @return array
     */
    protected function getTopicIdsFromForumId($iForumId)
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT topicId FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . 'WHERE forumId = :forumId'
        );
        $rStmt->bindValue(':forumId', $iForumId, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get Forum IDs from Category ID.
     *
     * @param int $iCategoryId
     *
     * @return array
     */
    protected function getForumIdsFromCatId($iCategoryId)
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT forumId FROM' . Db::prefix(DbTableName::FORUM) . 'WHERE categoryId = :categoryId'
        );
        $rStmt->bindValue(':categoryId', $iCategoryId, PDO::PARAM_INT);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Delete Messages from Forum ID.
     *
     * @param int $iForumId
     *
     * @return void
     */
    private function delMsgsFromForumId($iForumId)
    {
        $oTopicIds = $this->getTopicIdsFromForumId($iForumId);

        foreach ($oTopicIds as $oId) {
            $iId = (int)$oId->topicId;

            $rStmt = Db::getInstance()->prepare(
                'DELETE FROM' . Db::prefix(DbTableName::FORUM_MESSAGE) . 'WHERE topicId = :topicId'
            );
            $rStmt->bindValue(':topicId', $iId, PDO::PARAM_INT);
            $rStmt->execute();
        }
    }

    /**
     * Delete Messages and Topics from Category ID.
     *
     * @param int $iCategoryId
     *
     * @return void
     */
    private function delMsgsTopicsFromCatId($iCategoryId)
    {
        $oForumIds = $this->getForumIdsFromCatId($iCategoryId);

        foreach ($oForumIds as $oId) {
            $iId = (int)$oId->forumId;
            $this->delMsgsFromForumId($iId);

            $rStmt = Db::getInstance()->prepare(
                'DELETE FROM' . Db::prefix(DbTableName::FORUM_TOPIC) . 'WHERE forumId = :forumId'
            );
            $rStmt->bindValue(':forumId', $iId, PDO::PARAM_INT);
            $rStmt->execute();
        }
    }
}
