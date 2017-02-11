<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Model
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class BlogModel extends BlogCoreModel
{

    public function getCategory($iBlogId = null, $iOffset, $iLimit, $bCount = false)
    {
        $this->cache->start(self::CACHE_GROUP, 'category' . $iBlogId . $iOffset . $iLimit . $bCount, static::CACHE_TIME);
        if (!$oData = $this->cache->get())
        {
            $iOffset = (int) $iOffset;
            $iLimit = (int) $iLimit;

            if ($bCount)
            {
                $sSql = 'SELECT *, COUNT(c.blogId) AS totalCatBlogs FROM' . Db::prefix('BlogsDataCategories') . 'AS d INNER JOIN' . Db::prefix('BlogsCategories') . 'AS c ON d.categoryId = c.categoryId GROUP BY d.name ASC LIMIT :offset, :limit';
            }
            else
            {
                $sSqlBlogId = (isset($iBlogId)) ? ' INNER JOIN ' . Db::prefix('BlogsCategories') . 'AS c ON d.categoryId = c.categoryId WHERE c.blogId = :blogId ' : ' ';
                $sSql = 'SELECT * FROM' . Db::prefix('BlogsDataCategories') . 'AS d' . $sSqlBlogId . 'ORDER BY d.name ASC LIMIT :offset, :limit';
            }

            $rStmt = Db::getInstance()->prepare($sSql);

            if (isset($iBlogId)) $rStmt->bindParam(':blogId', $iBlogId, \PDO::PARAM_INT);
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
            $rStmt->execute();
            $oData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    public function addCategory($iCategoryId, $iBlogId)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('BlogsCategories') . '(categoryId, blogId) VALUES(:categoryId, :blogId)');
        $rStmt->bindParam(':categoryId', $iCategoryId, \PDO::PARAM_INT);
        $rStmt->bindParam(':blogId', $iBlogId, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    public function readPost($sPostId)
    {
        $this->cache->start(self::CACHE_GROUP, 'readPost' . $sPostId, static::CACHE_TIME);

        if (!$oData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT * FROM' . Db::prefix('Blogs') . 'AS b LEFT JOIN' . Db::prefix('BlogsCategories') . 'AS c ON b.blogId = c.blogId WHERE b.postId = :postId LIMIT 1');
            $rStmt->bindValue(':postId', $sPostId, \PDO::PARAM_STR);
            $rStmt->execute();
            $oData = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $this->cache->put($oData);
        }

        return $oData;
    }

    public function addPost($aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Blogs') . '(postId, langId, title, content, slogan, tags, pageTitle, metaDescription, metaKeywords, metaRobots, metaAuthor, metaCopyright, enableComment, createdDate)
            VALUES (:postId, :langId, :title, :content, :slogan, :tags, :pageTitle, :metaDescription, :metaKeywords, :metaRobots, :metaAuthor, :metaCopyright, :enableComment, :createdDate)');
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
        return $rStmt->execute();
    }

    public function category($sCategoryName, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $sSqlLimit = (!$bCount) ?  'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ?  '*' : 'COUNT(b.blogId) AS totalBlogs';

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Blogs') . 'AS b LEFT JOIN ' . Db::prefix('BlogsCategories') . 'AS c ON b.blogId = c.blogId LEFT JOIN' .
        Db::prefix('BlogsDataCategories') . 'AS d ON c.categoryId = d.categoryId WHERE d.name LIKE :name' . $sSqlOrder . $sSqlLimit);

        $rStmt->bindValue(':name', '%' . $sCategoryName . '%', \PDO::PARAM_STR);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int) $oRow->totalBlogs;
            unset($oRow);
        }

        return $mData;
    }

    public function search($mLooking, $bCount, $sOrderBy, $sSort, $iOffset, $iLimit)
    {
        $bCount = (bool) $bCount;
        $iOffset = (int) $iOffset;
        $iLimit = (int) $iLimit;

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $sSort);

        $sSqlLimit = (!$bCount) ?  'LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ?  '*' : 'COUNT(blogId) AS totalBlogs';

        if (ctype_digit($mLooking)) {
            $sSqlWhere = ' WHERE blogId = :looking';
        } else {
            $sSqlWhere = ' WHERE postId LIKE :looking OR title LIKE :looking OR
                pageTitle LIKE :looking OR content LIKE :looking OR tags LIKE :looking';
        }

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Blogs') . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount)
        {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

        $rStmt->execute();

        if (!$bCount)
        {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        }
        else
        {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int) $oRow->totalBlogs;
            unset($oRow);
        }

        return $mData;
    }

    public function getPostId($iBlogId)
    {
        $this->cache->start(self::CACHE_GROUP, 'postId' . $iBlogId, static::CACHE_TIME);

        if (!$sData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT postId FROM' . Db::prefix('Blogs') . ' WHERE blogId = :blogId LIMIT 1');
            $rStmt->bindValue(':blogId', $iBlogId, \PDO::PARAM_INT);
            $rStmt->execute();
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $sData = $oRow->postId;
            unset($oRow);
            $this->cache->put($sData);
        }

        return $sData;
    }

    public function postIdExists($sPostId)
    {
        $this->cache->start(self::CACHE_GROUP, 'postIdExists' . $sPostId, static::CACHE_TIME);

        if (!$bData = $this->cache->get())
        {
            $rStmt = Db::getInstance()->prepare('SELECT COUNT(postId) FROM'.Db::prefix('Blogs').'WHERE postId = :postId LIMIT 1');
            $rStmt->bindValue(':postId', $sPostId, \PDO::PARAM_STR);
            $rStmt->execute();
            $bData = ($rStmt->fetchColumn() == 1);
            Db::free($rStmt);
            $this->cache->put($bData);
        }

        return $bData;
    }

    public function deletePost($iBlogId)
    {
        $iBlogId = (int) $iBlogId;
        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('Blogs') . 'WHERE blogId = :blogId');
        $rStmt->bindValue(':blogId', $iBlogId, \PDO::PARAM_INT);
        return $rStmt->execute();
    }

    /**
     * @param integer $iBlogId
     * @return void
     */
    public function deleteCategory($iBlogId)
    {
        $iBlogId = (int) $iBlogId;

        $rStmt = Db::getInstance()->prepare('DELETE FROM' . Db::prefix('BlogsCategories') . 'WHERE blogId = :blogId');
        $rStmt->bindValue(':blogId', $iBlogId, \PDO::PARAM_INT);
        $rStmt->execute();
    }

     public function updatePost($sSection, $sValue, $iBlogId)
     {
         $this->orm->update('Blogs', $sSection, $sValue, 'blogId', $iBlogId);
     }

}
