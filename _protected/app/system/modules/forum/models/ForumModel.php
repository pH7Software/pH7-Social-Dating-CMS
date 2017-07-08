<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class ForumModel extends ForumCoreModel
{

    public function getCategory($iCategoryId = null, $iOffset = null, $iLimit = null)
    {
        $bIsLimit = isset($iOffset, $iLimit);

        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlLimit = ($bIsLimit) ? ' LIMIT :offset, :limit' : '';
        $sSqlCategoryId = (!empty($iCategoryId)) ? ' WHERE categoryId = :categoryId ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('ForumsCategories') . $sSqlCategoryId . 'ORDER BY title ASC' . $sSqlLimit);
        if (!empty($iCategoryId)) $rStmt->bindParam(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        if ($bIsLimit) $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        if ($bIsLimit) $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();

        return (!empty($iCategoryId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getTopic($sForumName, $iForumId, $sTopicSubject, $iTopicId, $iProfileId, $iApproved, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlProfileId = (isset($iProfileId)) ? 'AND t.profileId = :profileId ' : '';
        $sSqlMsg = (isset($sTopicSubject, $iTopicId)) ? ' AND (t.title LIKE :topicSubject AND t.topicId = :topicId) ' : '';

        $rStmt = Db::getInstance()->prepare('SELECT f.*, f.createdDate AS forumCreatedDate, f.updatedDate AS forumUpdatedDate, t.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Forums') .
            'AS f INNER JOIN' . Db::prefix('ForumsTopics') . 'AS t ON f.forumId = t.forumId LEFT JOIN' . Db::prefix('Members') .
            ' AS m ON t.profileId = m.profileId WHERE (t.forumId = :forumId AND f.name LIKE :forumName) ' . $sSqlMsg . $sSqlProfileId . ' AND (t.approved = :approved) ORDER BY t.createdDate DESC LIMIT :offset, :limit');

        $rStmt->bindValue(':forumName', $sForumName . '%', \PDO::PARAM_STR);
        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);

        if (isset($sTopicSubject, $iTopicId)) {
            $rStmt->bindValue(':topicSubject', $sTopicSubject . '%', \PDO::PARAM_STR);
            $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        }

        if (isset($iProfileId)) $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();

        return (isset($sTopicSubject, $iTopicId)) ? $rStmt->fetch(\PDO::FETCH_OBJ) : $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function addCategory($sTitle)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('ForumsCategories') . '(title) VALUES(:title)');
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function addForum($iCategoryId, $sTitle, $sDescription, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Forums') . '(categoryId, name, description, createdDate)
            VALUES(:categoryId, :title, :description, :createdDate)');

        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function addTopic($iProfileId, $iForumId, $sTitle, $sMessage, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('ForumsTopics') . '(profileId, forumId, title, message, createdDate)
            VALUES(:profileId, :forumId, :title, :message, :createdDate)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function addMessage($iProfileId, $iTopicId, $sMessage, $sCreatedDate)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('ForumsMessages') . '(profileId, topicId, message, createdDate)
            VALUES(:profileId, :topicId, :message, :createdDate)');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        $rStmt->bindValue(':message', $sMessage, \PDO::PARAM_STR);
        $rStmt->bindValue(':createdDate', $sCreatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updateCategory($iCategoryId, $sTitle)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('ForumsCategories') .
            'SET title = :title WHERE categoryId = :categoryId');

        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updateForum($iForumId, $iCategoryId, $sName, $sDescription, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Forums') .
            'SET categoryId = :categoryId, name = :name, description = :description, updatedDate = :updatedDate WHERE forumId = :forumId');

        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);
        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->bindValue(':name', $sName, \PDO::PARAM_STR);
        $rStmt->bindValue(':description', $sDescription, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updateTopic($iProfileId, $iTopicId, $sTitle, $sMessage, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('ForumsTopics') .
            'SET title = :title, message = :message, updatedDate = :updatedDate WHERE profileId = :profileId AND topicId = :topicId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        $rStmt->bindValue(':title', $sTitle, \PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    public function updateMessage($iProfileId, $iMessageId, $sMessage, $sUpdatedDate)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('ForumsMessages') .
            'SET message = :message, updatedDate = :updatedDate WHERE profileId = :profileId AND messageId = :messageId');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        $rStmt->bindValue(':message', $sMessage, \PDO::PARAM_STR);
        $rStmt->bindValue(':updatedDate', $sUpdatedDate, \PDO::PARAM_STR);
        return $rStmt->execute();
    }

    /**
     * Deletes the category and forums, topics and posts in it.
     *
     * @param integer $iCategoryId
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteCategory($iCategoryId)
    {
        // Topics of Forums & Messages of Topics
        $this->_delMsgsTopicsFromCatId($iCategoryId);

        // Forums of Category
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Forums') . 'WHERE categoryId = :categoryId');
        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->execute();

        // Category
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsCategories') . 'WHERE categoryId = :categoryId LIMIT 1');
        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Deletes the forum and the topics and posts in it.
     *
     * @param integer $iForumId
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteForum($iForumId)
    {
        // Messages of Topics
        $this->_delMsgsFromForumId($iForumId);

        // Topics of Forum
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsTopics') . 'WHERE forumId = :forumId');
        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);
        $rStmt->execute();

        // Forum
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Forums') . 'WHERE forumId = :forumId LIMIT 1');
        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Deletes the topic and posts in it.
     *
     * @param integer $iProfileId
     * @param integer $iTopicId
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteTopic($iProfileId, $iTopicId)
    {
        // Messages of Topic
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsMessages') . 'WHERE profileId = :profileId AND topicId = :topicId');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        $rStmt->execute();

        // Topic
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsTopics') . 'WHERE profileId = :profileId AND topicId = :topicId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Deletes posts.
     *
     * @param integer $iProfileId
     * @param integer $iMessageId
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function deleteMessage($iProfileId, $iMessageId)
    {
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsMessages') . 'WHERE profileId = :profileId AND messageId = :messageId LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':messageId', $iMessageId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * Search Topics.
     *
     * @param integer|string $mLooking (integer for Topic ID or string for a keyword)
     * @param boolean $bCount Put 'true' for count the topics or 'false' for the result of topics.
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @return integer|object (integer for the number topics returned or an object for the topics list)
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 't');

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? 'f.*, f.createdDate AS forumCreatedDate, f.updatedDate AS forumUpdatedDate, t.*, m.username, m.firstName, m.sex' : 'COUNT(t.topicId) AS totalTopics';
        $sSqlWhere = (ctype_digit($mLooking)) ? ' WHERE t.topicId = :looking ' : ' WHERE t.message LIKE :looking OR t.title LIKE :looking OR m.username LIKE :looking';
        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Forums') . 'AS f INNER JOIN' . Db::prefix('ForumsTopics') . 'AS t ON f.forumId = t.forumId LEFT JOIN' . Db::prefix('Members') . ' AS m ON t.profileId = m.profileId' . $sSqlWhere . $sSqlOrder . $sSqlLimit);
        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int)$oRow->totalTopics;
            unset($oRow);
        }

        return $mData;
    }

    public function getPostByProfile($iProfileId, $iApproved, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Forums') . ' AS f INNER JOIN ' . Db::prefix('ForumsTopics') .
            'AS t ON f.forumId = t.forumId WHERE t.profileId = :profileId AND t.approved = :approved GROUP BY t.topicId ORDER BY t.createdDate DESC LIMIT :offset, :limit');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function totalForums($iProfileId = null)
    {
        $sSqlProfileId = (!empty($iProfileId)) ? ' WHERE profileId = :profileId' : '';
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(forumId) AS totalForums FROM' . Db::prefix('Forums') . $sSqlProfileId);
        if (!empty($iProfileId)) $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->totalForums;
    }

    /**
     * Get the total number of topic.
     *
     * @param integer $iForumId Search by topic ID. Default NULL
     * @param integer $iProfileId Search by user ID. Default NULL
     * @return integer
     */
    public function totalTopics($iForumId = null, $iProfileId = null)
    {
        $sSql = (!empty($iForumId) ? ' WHERE forumId = :forumId' : (!empty($iProfileId) ? ' WHERE profileId = :profileId' : ''));
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(topicId) AS totalTopics FROM' . Db::prefix('ForumsTopics') . $sSql);
        (!empty($iForumId) ? $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT) : (!empty($iProfileId) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : ''));

        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->totalTopics;
    }

    public function totalMessages($iTopicId = null, $iProfileId = null)
    {
        $sSql = (!empty($iTopicId) ? ' WHERE topicId = :topicId' : (!empty($iProfileId) ? ' WHERE profileId = :profileId' : ''));
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(messageId) AS totalMessages FROM' . Db::prefix('ForumsMessages') . $sSql);
        (!empty($iTopicId) ? $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT) : (!empty($iProfileId) ? $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT) : ''));
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return (int)$oRow->totalMessages;
    }

    /**
     * Check Duplicate Topics.
     *
     * @param string $sCheckMsg
     * @param integer $iProfileId
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateTopic($iProfileId, $sCheckMsg)
    {
        return Framework\Mvc\Model\Spam::detectDuplicate($sCheckMsg, 'message', 'topicId', $iProfileId, 'ForumsTopics');
    }

    /**
     * Check Duplicate Messages.
     *
     * @param string $sCheckMsg
     * @param integer $iProfileId
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public function isDuplicateMessage($iProfileId, $sCheckMsg)
    {
        return Framework\Mvc\Model\Spam::detectDuplicate($sCheckMsg, 'message', 'messageId', $iProfileId, 'ForumsMessages');
    }

    /**
     * To prevent spam!
     * Waiting time to send a new topic in the forum.
     *
     * @param integer $iProfileId
     * @param integer $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitTopic($iProfileId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT topicId FROM' . Db::prefix('ForumsTopics') .
            'WHERE profileId = :profileId AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');

        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 0);
    }

    /**
     * To prevent spam!
     * Waiting time to send a reply message in the same topic.
     *
     * @param integer $iTopicId
     * @param integer $iProfileId
     * @param integer $iWaitTime In minutes!
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitReply($iTopicId, $iProfileId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT messageId FROM' . Db::prefix('ForumsMessages') .
            'WHERE topicId = :topicId AND profileId = :profileId AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');

        $rStmt->bindValue(':topicId', $iTopicId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 0);
    }

    /**
     * Get Topic IDs from Forum ID.
     *
     * @param integer $iForumId
     * @return object
     */
    protected function getTopicIdsFromForumId($iForumId)
    {
        $rStmt = Db::getInstance()->prepare('SELECT topicId FROM' . Db::prefix('ForumsTopics') . 'WHERE forumId = :forumId');
        $rStmt->bindValue(':forumId', $iForumId, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Get Forum IDs from Category ID.
     *
     * @param integer $iCategoryId
     * @return object
     */
     protected function getForumIdsFromCatId($iCategoryId)
     {
        $rStmt = Db::getInstance()->prepare('SELECT forumId FROM' . Db::prefix('Forums') . 'WHERE categoryId = :categoryId');
        $rStmt->bindValue(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->execute();
        return $rStmt->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * Delete Messages from Forum ID.
     *
     * @param integer $iForumId
     * @return void
     */
    private function _delMsgsFromForumId($iForumId)
    {
        $oTopicIds = $this->getTopicIdsFromForumId($iForumId);

        foreach ($oTopicIds as $oId)
        {
            $iId = (int) $oId->topicId;

            $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsMessages') . 'WHERE topicId = :topicId');
            $rStmt->bindValue(':topicId', $iId, \PDO::PARAM_INT);
            $rStmt->execute();
        }
    }

    /**
     * Delete Messages and Topics from Category ID.
     *
     * @param integer $iCategoryId
     * @return void
     */
    private function _delMsgsTopicsFromCatId($iCategoryId)
    {
        $oForumIds = $this->getForumIdsFromCatId($iCategoryId);

        foreach ($oForumIds as $oId)
        {
            $iId = (int) $oId->forumId;
            $this->_delMsgsFromForumId($iId);

            $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('ForumsTopics') . 'WHERE forumId = :forumId');
            $rStmt->bindValue(':forumId', $iId, \PDO::PARAM_INT);
            $rStmt->execute();
        }
    }

}
