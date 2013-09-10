<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Inc / Class
 */
namespace PH7;
use PH7\Framework\Config\Config;

class Blog extends WriteCore
{

    /**
     * Sets the Blog Thumbnail.
     *
     * @param \PH7\Framework\File\File $oFile
     * @param object $oPost
     * @return void
     */
    public function setThumb(Framework\File\File $oFile, $oPost)
    {
        if (!empty($_FILES['thumb']['tmp_name']))
        {
            $oImage = new Framework\Image\Image($_FILES['thumb']['tmp_name']);
            if (!$oImage->validate())
            {
                \PFBC\Form::setError('form_blog', Form::wrongImgFileTypeMsg());
            }
            else
            {
                /**
                 * The method deleteFile first test if the file exists, if so it delete the file.
                 */
                $sPathName = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog/' . PH7_IMG . $oPost->blogId;
                $oFile->deleteFile($sPathName); // It erases the old thumbnail
                $oFile->createDir($sPathName);
                $oImage->square(100);
                $oImage->save($sPathName . '/thumb.png');
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
        $sUrl = PH7_URL_DATA_SYS_MOD . 'blog/' . PH7_IMG;
        $sThumb = (is_file(PH7_PATH_PUBLIC_DATA_SYS_MOD . 'blog/' . PH7_IMG . $iBlogId . '/thumb.png')) ? $iBlogId . '/thumb.png' : 'default_thumb.jpg';
        return $sUrl . $sThumb;
    }

    /**
     * Checks the Post ID.
     *
     * @param string $sPostId
     * @return boolean
     */
    public function checkPostId($sPostId)
    {
        return (preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) && !(new BlogModel)->postIdExists($sPostId)) ? true : false;
    }

}
