<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Inc / Class
 */
namespace PH7;
use PH7\Framework\Config\Config;
use PH7\Framework\File\File;

class Blog extends WriteCore
{
    /**
     * Sets the Blog Thumbnail.
     *
     * @param object $oPost
     * @param \PH7\Framework\File\File $oFile
     * @return void
     */
    public function setThumb($oPost, File $oFile)
    {
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $oImage = new Framework\Image\Image($_FILES['thumb']['tmp_name']);
            if (!$oImage->validate()) {
                \PFBC\Form::setError('form_blog', Form::wrongImgFileTypeMsg());
            } else {
                /**
                 * The method deleteFile first test if the file exists, if so it delete the file.
                 */
                $sPathName = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog' . PH7_DS . PH7_IMG . $oPost->blogId;
                $oFile->deleteFile($sPathName); // It erases the old thumbnail
                $oFile->createDir($sPathName);
                $oImage->square(100);
                $oImage->save($sPathName . PH7_DS . static::THUMBNAIL_FILENAME);

                // Clear the Web browser cache
                (new Framework\Navigation\Browser)->noCache();
            }
            unset($oImage);
        }
    }

    /**
     * Get the thumbnail of blog post.
     *
     * @param integer $iBlogId The ID of the Blog Post.
     * @return string The URL of the thumbnail.
     */
    public static function getThumb($iBlogId)
    {
        $sFullPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog' . PH7_DS . PH7_IMG . $iBlogId . PH7_DS . static::THUMBNAIL_FILENAME;
        $sThumb = (is_file($sFullPath)) ? $iBlogId . PH7_SH . static::THUMBNAIL_FILENAME . '?v=' . File::version($sFullPath) : 'default_thumb.jpg';
        return PH7_URL_DATA_SYS_MOD . 'blog' . PH7_SH . PH7_IMG . $sThumb;
    }

    /**
     * Checks the Post ID.
     *
     * @param string $sPostId
     * @param BlogModel $oBlogModel
     * @return boolean
     */
    public function checkPostId($sPostId, BlogModel $oBlogModel)
    {
        return (preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) && !$oBlogModel->postIdExists($sPostId));
    }

    public static function clearCache()
    {
        (new Framework\Cache\Cache)->start(BlogModel::CACHE_GROUP, null, null)->clear();
    }
}
