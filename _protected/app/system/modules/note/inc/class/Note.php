<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Util\Various;

class Note extends WriteCore
{
    const MAX_CATEGORY_ALLOWED = 3;

    /**
     * Sets the Note Thumbnail.
     *
     * @param object $oPost
     * @param \PH7\NoteModel $oNoteModel
     * @param \PH7\Framework\File\File $oFile
     * @return void
     */
    public function setThumb($oPost, NoteModel $oNoteModel, Framework\File\File $oFile)
    {
        if (!empty($_FILES['thumb']['tmp_name'])) {
            $oImage = new Framework\Image\Image($_FILES['thumb']['tmp_name']);
            if (!$oImage->validate()) {
                \PFBC\Form::setError('form_note', Form::wrongImgFileTypeMsg());
            } else {
                /**
                 * The method deleteFile first test if the file exists, if so it delete the file.
                 */
                $sPathName = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'note/' . PH7_IMG . $oPost->username . PH7_SH;
                $oFile->deleteFile($sPathName); // It erases the old thumbnail
                $oFile->createDir($sPathName);
                $sFileName = Various::genRnd($oImage->getFileName(), 20) . PH7_DOT . $oImage->getExt();
                $oImage->square(100);
                $oImage->save($sPathName . $sFileName);
                $oNoteModel->updatePost('thumb', $sFileName, $oPost->noteId, $oPost->profileId);
            }
            unset($oImage);
        }
    }

    /**
     * Checks the Post ID.
     *
     * @param string $sPostId
     * @param integer $iProfileId
     * @param NoteModel $oNoteModel
     * @return boolean
     */
    public function checkPostId($sPostId, $iProfileId, NoteModel $oNoteModel)
    {
        return (preg_match('#^' . Config::getInstance()->values['module.setting']['post_id.pattern'] . '$#', $sPostId) && !$oNoteModel->postIdExists($sPostId, $iProfileId));
    }

    public static function clearCache()
    {
        (new Framework\Cache\Cache)->start(NoteModel::CACHE_GROUP, null, null)->clear();
    }
}
