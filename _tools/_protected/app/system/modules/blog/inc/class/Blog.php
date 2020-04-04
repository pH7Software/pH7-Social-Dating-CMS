<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Config\Config;
use PH7\Framework\File\File;
use PH7\Framework\Image\Image;
use PH7\Framework\Navigation\Browser;
use stdClass;

class Blog extends WriteCore
{
    const THUMBNAIL_IMAGE_SIZE = 100;

    /**
     * Sets the Blog Thumbnail.
     *
     * @param stdClass $oPost
     * @param File $oFile
     *
     * @return void
     *
     * @throws \PH7\Framework\File\TooLargeException
     * @throws \PH7\Framework\File\Permission\PermissionException
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function setThumb(stdClass $oPost, File $oFile)
    {
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $oImage = new Image($_FILES['thumb']['tmp_name']);
            if (!$oImage->validate()) {
                \PFBC\Form::setError('form_blog', Form::wrongImgFileTypeMsg());
            } else {
                /**
                 * File::deleteFile() tests first if the file exists, and then deletes the file
                 */
                $sPathName = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog' . PH7_DS . PH7_IMG . $oPost->blogId;
                $oFile->deleteFile($sPathName); // It erases the old thumbnail
                $oFile->createDir($sPathName);
                $oImage->square(static::THUMBNAIL_IMAGE_SIZE);
                $oImage->save($sPathName . PH7_DS . static::THUMBNAIL_FILENAME);

                // Clear the Web browser cache
                (new Browser)->noCache();
            }
            unset($oImage);
        }
    }

    /**
     * Get the thumbnail of blog post.
     *
     * @param int $iBlogId The ID of the Blog Post.
     *
     * @return string The URL of the thumbnail.
     */
    public static function getThumb($iBlogId)
    {
        $sFullPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog' . PH7_DS . PH7_IMG . $iBlogId . PH7_DS . static::THUMBNAIL_FILENAME;

        if (is_file($sFullPath)) {
            $sThumb = $iBlogId . PH7_SH . static::THUMBNAIL_FILENAME . '?v=' . File::version($sFullPath);
        } else {
            $sThumb = static::DEFAULT_THUMBNAIL_FILENAME;
        }

        return PH7_URL_DATA_SYS_MOD . 'blog' . PH7_SH . PH7_IMG . $sThumb;
    }

    /**
     * Checks the Post ID.
     *
     * @param string $sPostId
     * @param BlogModel $oBlogModel
     *
     * @return bool
     */
    public function checkPostId($sPostId, BlogModel $oBlogModel)
    {
        return preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) &&
            !$oBlogModel->postIdExists($sPostId);
    }

    public static function clearCache()
    {
        (new Cache)->start(BlogModel::CACHE_GROUP, null, null)->clear();
    }
}
