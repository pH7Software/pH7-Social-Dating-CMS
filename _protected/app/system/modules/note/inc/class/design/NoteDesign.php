<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class NoteDesign extends WriteDesignCoreModel
{
    const POST_AVATAR_SIZE = 100;

    /**
     * @param object $oNoteModel
     *
     * @return void Output the URL of the thumbnail.
     */
    public static function thumb($oNoteModel)
    {
        echo '<div itemprop="image">';
        if (!empty($oNoteModel->thumb)) {
            echo '<a href="', Uri::get('note', 'main', 'read', $oNoteModel->username . ',' . $oNoteModel->postId), '" class="pic thumb" data-load="ajax">';
            echo '<img src="', PH7_URL_DATA_SYS_MOD, 'note/', PH7_IMG, $oNoteModel->username, PH7_SH, $oNoteModel->thumb, '" alt="', $oNoteModel->pageTitle, '" title="', $oNoteModel->pageTitle, '" />';
            echo '</a>';
        } else {
            (new AvatarDesignCore)->get(
                $oNoteModel->username,
                $oNoteModel->firstName,
                $oNoteModel->sex,
                self::POST_AVATAR_SIZE
            );
        }
        echo '</div>';
    }
}
