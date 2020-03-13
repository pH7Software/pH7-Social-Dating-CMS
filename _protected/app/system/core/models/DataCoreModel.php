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

class DataCoreModel extends Model
{
    const TB_PICTURE = DbTableName::PICTURE;
    const TB_VIDEO = DbTableName::VIDEO;
    const MAX_ITEMS = 1000;

    /**
     * Get images or videos from either Videos or Pictures table.
     *
     * @param string $sTable
     * @param string $sOrder
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getPicsVids($sTable, $sOrder, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlQuery = 'SELECT data.*, m.username FROM' . Db::prefix($sTable) . 'AS data INNER JOIN' .
            Db::prefix(DbTableName::MEMBER) . 'AS m ON data.profileId = m.profileId WHERE data.approved = 1 ORDER BY ' .
            $sOrder . ' DESC LIMIT :offset, :limit';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);

        $rStmt->execute();
        $aData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aData;
    }

    /**
     * @param string $sOrder
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return array
     */
    public function getForumsPosts($sOrder, $iOffset, $iLimit)
    {
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;

        $sSqlQuery = 'SELECT f.name, t.title, t.message, t.createdDate, t.updatedDate, t.forumId, t.topicId, m.username FROM' .
            Db::prefix(DbTableName::FORUM) . 'AS f INNER JOIN' . Db::prefix(DbTableName::FORUM_TOPIC) .
            'AS t ON f.forumId = t.forumId LEFT JOIN' . Db::prefix(DbTableName::MEMBER) .
            ' AS m ON t.profileId = m.profileId WHERE t.approved = 1 ORDER BY ' . $sOrder . ' DESC LIMIT :offset, :limit';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindParam(':offset', $iOffset, PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, PDO::PARAM_INT);

        $rStmt->execute();
        $aData = $rStmt->fetchAll(PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $aData;
    }

    public function getProfiles()
    {
        return (new UserCoreModel)->getProfiles(SearchCoreModel::LATEST, 0, static::MAX_ITEMS);
    }

    public function getBlogs()
    {
        return (new BlogCoreModel)->getPosts(0, static::MAX_ITEMS, SearchCoreModel::UPDATED);
    }

    public function getNotes()
    {
        return (new NoteCoreModel)->getPosts(0, static::MAX_ITEMS, SearchCoreModel::UPDATED);
    }

    public function getForums()
    {
        return (new ForumCoreModel)->getForum(
            null,
            0,
            static::MAX_ITEMS,
            ForumCoreModel::UPDATED
        );
    }

    public function getForumsTopics()
    {
        return $this->getForumsPosts(SearchCoreModel::CREATED, 0, static::MAX_ITEMS);
    }

    /**
     * @param int $iTopicId
     *
     * @return array|false|stdClass
     */
    public function getForumsMessages($iTopicId)
    {
        return (new ForumCoreModel)->getMessage(
            $iTopicId,
            null,
            null,
            '1',
            0,
            static::MAX_ITEMS,
            Db::DESC
        );
    }

    public function getCommentsProfiles()
    {
        return (new CommentCoreModel)->gets('profile');
    }

    public function getCommentsBlogs()
    {
        return (new CommentCoreModel)->gets('blog');
    }

    public function getCommentsNotes()
    {
        return (new CommentCoreModel)->gets('note');
    }

    public function getCommentsPictures()
    {
        return (new CommentCoreModel)->gets('picture');
    }

    public function getCommentsVideos()
    {
        return (new CommentCoreModel)->gets('video');
    }

    public function getCommentsGames()
    {
        return (new CommentCoreModel)->gets('game');
    }

    public function getRecipientCommentsProfiles($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'profile'
        );
    }

    public function getRecipientCommentsBlogs($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'blog'
        );
    }

    public function getRecipientCommentsNotes($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'note'
        );
    }

    public function getRecipientCommentsPictures($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'picture'
        );
    }

    public function getRecipientCommentsVideos($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'video'
        );
    }

    public function getRecipientCommentsGames($iRecipientId)
    {
        return (new CommentCoreModel)->read(
            $iRecipientId,
            '1',
            0,
            static::MAX_ITEMS,
            'game'
        );
    }

    public function getAlbumsPictures()
    {
        return (new PictureCoreModel)->album(
            null,
            null,
            '1',
            0,
            static::MAX_ITEMS,
            SearchCoreModel::CREATED
        );
    }

    public function getPictures()
    {
        return $this->getPicsVids(
            static::TB_PICTURE,
            SearchCoreModel::CREATED,
            0,
            static::MAX_ITEMS
        );
    }

    public function getAlbumsVideos()
    {
        return (new VideoCoreModel)->album(
            null,
            null,
            '1',
            0,
            static::MAX_ITEMS,
            SearchCoreModel::CREATED
        );
    }

    public function getVideos()
    {
        return $this->getPicsVids(
            static::TB_VIDEO,
            SearchCoreModel::CREATED,
            0,
            static::MAX_ITEMS
        );
    }

    public function getGames()
    {
        return (new GameCoreModel)->get(
            null,
            null,
            0,
            static::MAX_ITEMS,
            SearchCoreModel::ADDED_DATE
        );
    }
}
