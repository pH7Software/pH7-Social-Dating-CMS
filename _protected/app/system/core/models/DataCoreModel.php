<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class DataCoreModel extends Framework\Mvc\Model\Engine\Model
{

    const TB_PICTURE = 'Pictures', TB_VIDEO = 'Videos', MAX_ITEMS = 1000;

    public function getPicsVids($sTable, $sOrder, $iOffset, $iLimit)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT data.*, m.username FROM' . Db::prefix($sTable) . 'AS data INNER JOIN' . Db::prefix('Members') . 'AS m ON data.profileId = m.profileId WHERE data.approved=1 ORDER BY ' . $sOrder . ' DESC LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);

        $rStmt->execute();
        $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oData;
    }

    public function getForumsPosts($sOrder, $iOffset, $iLimit)
    {
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $rStmt = Db::getInstance()->prepare('SELECT f.name, t.title, t.message, t.createdDate, t.updatedDate, t.forumId, t.topicId, m.username FROM' . Db::prefix('Forums') . 'AS f INNER JOIN' . Db::prefix('ForumsTopics') . 'AS t ON f.forumId = t.forumId LEFT JOIN' . Db::prefix('Members') . ' AS m ON t.profileId = m.profileId WHERE t.approved=1 ORDER BY ' . $sOrder . ' DESC LIMIT :offset, :limit');
        $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
        $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);

        $rStmt->execute();
        $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
        Db::free($rStmt);
        return $oData;
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
        return (new ForumCoreModel)->getForum(null, 0, static::MAX_ITEMS, ForumCoreModel::UPDATED);
    }

    public function getForumsTopics()
    {
        return $this->getForumsPosts(SearchCoreModel::CREATED, 0, static::MAX_ITEMS);
    }

    public function getForumsMessages($iTopicId)
    {
        return (new ForumCoreModel)->getMessage($iTopicId, null, null, 1, 0, static::MAX_ITEMS, Db::DESC);
    }

    public function getCommentsProfiles()
    {
        return (new CommentCoreModel)->gets('Profile');
    }

    public function getCommentsBlogs()
    {
        return (new CommentCoreModel)->gets('Blog');
    }

    public function getCommentsNotes()
    {
        return (new CommentCoreModel)->gets('Note');
    }

    public function getCommentsPictures()
    {
        return (new CommentCoreModel)->gets('Picture');
    }

    public function getCommentsVideos()
    {
        return (new CommentCoreModel)->gets('Video');
    }

    public function getCommentsGames()
    {
        return (new CommentCoreModel)->gets('Game');
    }

    public function getRecipientCommentsProfiles($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Profile');
    }

    public function getRecipientCommentsBlogs($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Blog');
    }

    public function getRecipientCommentsNotes($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Note');
    }

    public function getRecipientCommentsPictures($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Picture');
    }

    public function getRecipientCommentsVideos($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Video');
    }

    public function getRecipientCommentsGames($iRecipientId)
    {
        return (new CommentCoreModel)->read($iRecipientId, 1, 0, static::MAX_ITEMS, 'Game');
    }

    public function getAlbumsPictures()
    {
        return (new PictureCoreModel)->album(null, null, 1, 0, static::MAX_ITEMS, SearchCoreModel::CREATED);
    }

    public function getPictures()
    {
        return $this->getPicsVids(static::TB_PICTURE, SearchCoreModel::CREATED, 0, static::MAX_ITEMS);
    }

    public function getAlbumsVideos()
    {
        return (new VideoCoreModel)->album(null, null, 1, 0, static::MAX_ITEMS, SearchCoreModel::CREATED);
    }

    public function getVideos()
    {
        return $this->getPicsVids(static::TB_VIDEO, SearchCoreModel::CREATED, 0, static::MAX_ITEMS);
    }

    public function getGames()
    {
        return (new GameCoreModel)->get(null, null, 0, static::MAX_ITEMS, SearchCoreModel::ADDED_DATE);
    }

}
