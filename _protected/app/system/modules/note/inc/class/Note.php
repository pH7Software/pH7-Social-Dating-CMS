<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Config\Config;
use PH7\Framework\File\File;
use PH7\Framework\Image\Image;
use PH7\Framework\Util\Various;
use stdClass;

class Note extends WriteCore
{
    const MAX_CATEGORY_ALLOWED = 3;
    const THUMBNAIL_IMAGE_SIZE = 100;
    const FILENAME_LENGTH = 20;

    /**
     * Sets the Note Thumbnail.
     *
     * @param stdClass $oPost
     * @param NoteModel $oNoteModel
     * @param File $oFile
     *
     * @return void
     *
     * @throws \PH7\Framework\File\TooLargeException
     * @throws \PH7\Framework\File\Permission\PermissionException
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function setThumb(stdClass $oPost, NoteModel $oNoteModel, File $oFile)
    {
        $oImage = new Image($_FILES['thumb']['tmp_name']);
        if (!$oImage->validate()) {
            \PFBC\Form::setError('form_note', Form::wrongImgFileTypeMsg());
        } else {
            /**
             * File::deleteFile() tests first if the file exists, and then deletes the file
             */
            $sPathName = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'note/' . PH7_IMG . $oPost->username . PH7_SH;
            $oFile->deleteFile($sPathName); // It erases the old thumbnail
            $oFile->createDir($sPathName);
            $sFileName = Various::genRnd($oImage->getFileName(), self::FILENAME_LENGTH) . PH7_DOT . $oImage->getExt();
            $oImage->square(static::THUMBNAIL_IMAGE_SIZE);
            $oImage->save($sPathName . $sFileName);
            $oNoteModel->updatePost('thumb', $sFileName, $oPost->noteId, $oPost->profileId);
        }
        unset($oImage);
    }

    /**
     * @return bool
     */
    public function isThumbnailUploaded()
    {
        return !empty($_FILES['thumb']['tmp_name']);
    }

    /**
     * Checks the Post ID.
     *
     * @param string $sPostId
     * @param int $iProfileId
     * @param NoteModel $oNoteModel
     *
     * @return bool
     */
    public function checkPostId($sPostId, $iProfileId, NoteModel $oNoteModel)
    {
        return preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) &&
            !$oNoteModel->postIdExists($sPostId, $iProfileId);
    }

    public static function clearCache()
    {
        (new Cache)->start(NoteModel::CACHE_GROUP, null, null)->clear();
    }
}
