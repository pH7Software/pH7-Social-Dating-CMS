<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute;

class NoteDesign
{

    /**
     * @param object $oNoteModel
     * @return string The URL of the thumbnail.
     */
    public static function getThumb($oNoteModel)
    {
        echo '<div class="pic thumb">';

        if(!empty($oNoteModel->thumb))
            echo '<a href="', UriRoute::get('note','main','read', $oNoteModel->username . ',' . $oNoteModel->postId), '"><img src="', PH7_URL_DATA_SYS_MOD, 'note/', PH7_IMG, $oNoteModel->username, '/', $oNoteModel->thumb, '" alt="', $oNoteModel->pageTitle, '" title="', $oNoteModel->pageTitle, '" /></a>';
        else
            (new AvatarDesignCore)->get($oNoteModel->username, $oNoteModel->firstName, $oNoteModel->sex, 100);

        echo '</div>';
    }

}
