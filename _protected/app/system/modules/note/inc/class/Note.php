<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Config\Config;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;
use PH7\Framework\File\Permission\PermissionException;
use PH7\Framework\File\TooLargeException;
use PH7\Framework\Image\FileStorage as FileStorageImage;
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
     * @throws TooLargeException
     * @throws PermissionException
     * @throws PH7InvalidArgumentException
     */
    public function setThumb(stdClass $oPost, NoteModel $oNoteModel, File $oFile): void
    {
        $oImage = new FileStorageImage($_FILES['thumb']['tmp_name']);
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

    public function isThumbnailUploaded(): bool
    {
        return !empty($_FILES['thumb']['tmp_name']);
    }

    public function checkPostId(string $sPostId, int $iProfileId, NoteModel $oNoteModel): bool
    {
        return preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) &&
            !$oNoteModel->postIdExists($sPostId, $iProfileId);
    }

    public static function clearCache(): void
    {
        (new Cache)->start(NoteModel::CACHE_GROUP, null, null)->clear();
    }
}
