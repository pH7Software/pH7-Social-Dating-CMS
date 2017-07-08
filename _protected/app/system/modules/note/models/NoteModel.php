<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class NoteModel extends NoteCoreModel
{
    /**
     * @param integer|null $iNoteId
     * @param integer $iOffset
     * @param integer $iLimit
     * @param boolean $bCount
     *
     * @return \stdClass
     */
    public function getCategory($iNoteId = null, $iOffset, $iLimit, $bCount = false)
    {
        $this->cache->start(self::CACHE_GROUP, 'category' . $iNoteId . $iOffset . $iLimit . $bCount, static::CACHE_TIME);
        if (!$oData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            if ($bCount) {
                $sSql = 'SELECT *, COUNT(c.noteId) AS totalCatNotes FROM' . Db::prefix('NotesDataCategories') . 'AS d INNER JOIN' . Db::prefix('NotesCategories') . 'AS c ON d.categoryId = c.categoryId GROUP BY d.name ASC LIMIT :offset, :limit';
            } else {
                $sSqlNoteId = (isset($iNoteId)) ? ' INNER JOIN ' . Db::prefix('NotesCategories') . 'AS c ON d.categoryId = c.categoryId WHERE c.noteId = :noteId ' : ' ';
                $sSql = 'SELECT * FROM' . Db::prefix('NotesDataCategories') . 'AS d' . $sSqlNoteId . 'ORDER BY d.name ASC LIMIT :offset, :limit';
            }

            $rStmt = Db::getInstance()->prepare($sSql);

            if (isset($iNoteId)) {
                $rStmt->bindParam(':noteId', $iNoteId, \PDO::PARAM_INT);
            }

            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * @param integer $iOffset
     * @param integer $iLimit
     * @param boolean $bCount
     *
     * @return \stdClass
     */
    public function getAuthor($iOffset, $iLimit, $bCount = false)
    {
        $this->cache->start(self::CACHE_GROUP, 'author' . $iOffset . $iLimit . $bCount, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $iOffset = (int)$iOffset;
            $iLimit = (int)$iLimit;

            $sSelect = ($bCount) ? '*, COUNT(n.noteId) AS totalAuthors' : '*';

            $rStmt = Db::getInstance()->prepare('SELECT ' . $sSelect . ' FROM' . Db::prefix('Notes') . 'AS n INNER JOIN' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId GROUP BY m.username ASC LIMIT :offset, :limit');

            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * @param integer $iCategoryId
     * @param integer $iNoteId
     * @param integer $iProfileId
     */
    public function addCategory($iCategoryId, $iNoteId, $iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('NotesCategories') . '(categoryId, noteId, profileId) VALUES(:categoryId, :noteId, :profileId)');
        $rStmt->bindParam(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->bindParam(':noteId', $iNoteId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    /**
     * @param string $sPostId
     * @param integer $iProfileId
     * @param integer $iApproved
     *
     * @return \stdClass
     */
    public function readPost($sPostId, $iProfileId, $iApproved = 1)
    {
        $this->cache->start(self::CACHE_GROUP, 'readPost' . $sPostId . $iProfileId . $iApproved, static::CACHE_TIME);

        if (!$oData = $this->cache->get()) {
            $sSqlApproved = (isset($iApproved)) ? ' AND approved = :approved' : '';

            $rStmt = Db::getInstance()->prepare('SELECT n.*, c.*, m.username, m.firstName, m.sex FROM' . Db::prefix('Notes') . 'AS n LEFT JOIN' . Db::prefix('NotesCategories') . 'AS c ON n.noteId = c.noteId INNER JOIN' . Db::prefix('Members') . ' AS m ON n.profileId = m.profileId WHERE n.profileId = :profileId AND n.postId = :postId' . $sSqlApproved . ' LIMIT 1');
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->bindValue(':postId', $sPostId, \PDO::PARAM_STR);
            if (isset($iApproved)) {
                $rStmt->bindValue(':approved', $iApproved, \PDO::PARAM_INT);
            }
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    /**
     * @param array $aData
     *
     * @return boolean
     */
    public function addPost(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Notes') .
            '(profileId, postId, langId, title, content, slogan, tags, pageTitle, metaDescription, metaKeywords, metaRobots, metaAuthor, metaCopyright, enableComment, createdDate, approved)
            VALUES (:profileId, :postId, :langId, :title, :content, :slogan, :tags, :pageTitle, :metaDescription, :metaKeywords, :metaRobots, :metaAuthor, :metaCopyright, :enableComment, :createdDate, :approved)');

        $rStmt->bindValue(':profileId', $aData['profile_id'], \PDO::PARAM_INT);
        $rStmt->bindValue(':postId', $aData['post_id'], \PDO::PARAM_STR);
        $rStmt->bindValue(':langId', $aData['lang_id'], \PDO::PARAM_STR);
        $rStmt->bindValue(':title', $aData['title'], \PDO::PARAM_STR);
        $rStmt->bindValue(':content', $aData['content'], \PDO::PARAM_STR);
        $rStmt->bindValue(':slogan', $aData['slogan'], \PDO::PARAM_STR);
        $rStmt->bindValue(':tags', $aData['tags'], \PDO::PARAM_STR);
        $rStmt->bindValue(':pageTitle', $aData['page_title'], \PDO::PARAM_STR);
        $rStmt->bindValue(':metaDescription', $aData['meta_description'], \PDO::PARAM_STR);
        $rStmt->bindValue(':metaKeywords', $aData['meta_keywords'], \PDO::PARAM_STR);
        $rStmt->bindValue(':metaRobots', $aData['meta_robots'], \PDO::PARAM_STR);
        $rStmt->bindValue(':metaAuthor', $aData['meta_author'], \PDO::PARAM_STR);
        $rStmt->bindValue(':metaCopyright', $aData['meta_copyright'], \PDO::PARAM_STR);
        $rStmt->bindValue(':enableComment', $aData['enable_comment'], \PDO::PARAM_INT);
        $rStmt->bindValue(':createdDate', $aData['created_date'], \PDO::PARAM_STR);
        $rStmt->bindValue(':approved', $aData['approved'], \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param string $sCategoryName
     * @param boolean $bCount
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return integer|\stdClass
     */
    public function category($sCategoryName, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $sCategoryName = trim($sCategoryName);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'n');

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? 'n.*, c.*, d.*, m.username, m.firstName, m.sex' : 'COUNT(n.noteId) AS totalNotes';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Notes') . 'AS n LEFT JOIN ' . Db::prefix('NotesCategories') . 'AS c ON n.noteId = c.noteId LEFT JOIN' .
            Db::prefix('NotesDataCategories') . 'AS d ON c.categoryId = d.categoryId INNER JOIN' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId WHERE d.name LIKE :name' . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':name', '%' . $sCategoryName . '%', \PDO::PARAM_STR);

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
            $mData = (int)$oRow->totalNotes;
            unset($oRow);
        }

        return $mData;
    }

    /**
     * @param string $sAuthor
     * @param boolean $bCount
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     *
     * @return integer|\stdClass
     */
    public function author($sAuthor, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $sAuthor = trim($sAuthor);

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'n');

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? 'n.*, m.username, m.firstName, m.sex' : 'COUNT(m.profileId) AS totalAuthors';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Notes') . 'AS n
                INNER JOIN' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId WHERE m.username LIKE :name' . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':name', '%' . $sAuthor . '%', \PDO::PARAM_STR);

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
            $mData = (int)$oRow->totalAuthors;
            unset($oRow);
        }

        return $mData;
    }

    /**
     * @param integer|string $mLooking Integer for post ID or string for a keyword
     * @param boolean $bCount Put 'true' for count the notes or 'false' for the result of notes.
     * @param string $sOrderBy
     * @param integer $iSort
     * @param integer $iOffset
     * @param integer $iLimit
     * @param integer $iApproved (0 = Unmoderated | 1 = Approved | NULL = unmoderated and approved) Default 1
     *
     * @return integer|\stdClass (integer for the number notes returned or an object containing the notes list)
     */
    public function search($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit, $iApproved = 1)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlApproved = (isset($iApproved)) ? ' AND (approved = :approved)' : '';
        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort, 'n');

        $sSqlLimit = (!$bCount) ? 'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? 'n.*, m.username, m.firstName, m.sex' : 'COUNT(noteId) AS totalNotes';

        if (ctype_digit($mLooking)) {
            $sSqlWhere = ' WHERE (noteId = :looking)';
        } else {
            $sSqlWhere = ' WHERE (postId LIKE :looking OR title LIKE :looking OR
                pageTitle LIKE :looking OR content LIKE :looking OR tags LIKE :looking OR username LIKE :looking OR firstName LIKE :looking OR lastName LIKE :looking)';
        }

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Notes') . 'AS n INNER JOIN' . Db::prefix('Members') . 'AS m ON n.profileId = m.profileId' . $sSqlWhere . $sSqlApproved . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (isset($iApproved)) {
            $rStmt->bindParam(':approved', $iApproved, \PDO::PARAM_INT);
        }

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
            $mData = (int)$oRow->totalNotes;
            unset($oRow);
        }

        return $mData;
    }

    /**
     * @param integer $iNoteId
     *
     * @return string
     */
    public function getPostId($iNoteId)
    {
        $this->cache->start(self::CACHE_GROUP, 'postId' . $iNoteId, static::CACHE_TIME);

        if (!$sData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT postId FROM' . Db::prefix('Notes') . ' WHERE noteId = :noteId LIMIT 1');
            $rStmt->bindValue(':noteId', $iNoteId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = @$oRow->postId;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    /**
     * @param integer $sPostId
     * @param integer $iProfileId
     *
     * @return boolean
     */
    public function postIdExists($sPostId, $iProfileId)
    {
        $this->cache->start(self::CACHE_GROUP, 'postIdExists' . $sPostId . $iProfileId, static::CACHE_TIME);

        if (!$bData = $this->cache->get()) {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(postId) FROM' . Db::prefix('Notes') . 'WHERE postId = :postId AND profileId = :profileId LIMIT 1');
            $rStmt->bindValue(':postId', $sPostId, \PDO::PARAM_STR);
            $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
            $rStmt->execute();
            $bData = ($rStmt->fetchColumn() == 1);
            Db::free($rStmt);
            $this->cache->put($bData);
        }

        return $bData;
    }

    /**
     * @param integer $iNoteId
     * @param integer $iProfileId
     *
     * @return boolean
     */
    public function deletePost($iNoteId, $iProfileId)
    {
        $iNoteId = (int)$iNoteId;
        $iProfileId = (int)$iProfileId;

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Notes') . 'WHERE noteId = :noteId AND profileId = :profileId');
        $rStmt->bindValue(':noteId', $iNoteId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param integer $iNoteId
     */
    public function deleteCategory($iNoteId)
    {
        $iNoteId = (int)$iNoteId;

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('NotesCategories') . 'WHERE noteId = :noteId');
        $rStmt->bindValue(':noteId', $iNoteId, \PDO::PARAM_INT);
        $rStmt->execute();
    }

    /**
     * @param integer $iNoteId
     * @param integer $iProfileId
     */
    public function deleteThumb($iNoteId, $iProfileId)
    {
        $iNoteId = (int)$iNoteId;
        $iProfileId = (int)$iProfileId;

        $this->updatePost('thumb', null, $iNoteId, $iProfileId);
    }

    /**
     * @param string $sSection
     * @param string $sValue
     * @param integer $iNoteId
     * @param integer $iProfileId
     *
     * @return boolean
     */
    public function updatePost($sSection, $sValue, $iNoteId, $iProfileId)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Notes') . 'SET ' . $sSection . ' = :value WHERE noteId = :noteId AND profileId = :profileId');
        $rStmt->bindValue(':value', $sValue, \PDO::PARAM_STR);
        $rStmt->bindValue(':noteId', $iNoteId, \PDO::PARAM_INT);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);

        return $rStmt->execute();
    }

    /**
     * @param integer $iNoteId
     * @param integer $iStatus
     *
     * @return boolean
     */
    public function approved($iNoteId, $iStatus = 1)
    {
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix('Notes') . 'SET approved = :status WHERE noteId = :noteId');
        $rStmt->bindParam(':noteId', $iNoteId, \PDO::PARAM_INT);
        $rStmt->bindParam(':status', $iStatus, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * To prevent spam!
     *
     * @param integer $iProfileId
     * @param integer $iWaitTime In minutes
     * @param string $sCurrentTime In date format: 0000-00-00 00:00:00
     *
     * @return boolean Return TRUE if the weather was fine, otherwise FALSE
     */
    public function checkWaitSend($iProfileId, $iWaitTime, $sCurrentTime)
    {
        $rStmt = Db::getInstance()->prepare('SELECT noteId FROM' . Db::prefix('Notes') .
            'WHERE profileId = :profileId AND DATE_ADD(createdDate, INTERVAL :waitTime MINUTE) > :currentTime LIMIT 1');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':waitTime', $iWaitTime, \PDO::PARAM_INT);
        $rStmt->bindValue(':currentTime', $sCurrentTime, \PDO::PARAM_STR);
        $rStmt->execute();
        return ($rStmt->rowCount() === 0);
    }
}
