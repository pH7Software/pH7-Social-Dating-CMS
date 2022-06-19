<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class / Design
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use stdClass;

class NoteDesign extends WriteDesignCoreModel
{
    private const POST_AVATAR_SIZE = 100;

    public static function thumb(stdClass $oNoteModel): void
    {
        echo '<div itemprop="image">';
        if (!empty($oNoteModel->thumb)) {
            echo '<a href="', Uri::get(
                'note',
                'main',
                'read',
                $oNoteModel->username . ',' . $oNoteModel->postId
            ), '" class="pic" data-load="ajax">';
            echo '<img src="', PH7_URL_DATA_SYS_MOD, 'note/', PH7_IMG, $oNoteModel->username, PH7_SH, $oNoteModel->thumb, '" alt="', $oNoteModel->pageTitle, '" title="', $oNoteModel->pageTitle, ' loading="lazy" "class="thumb" />';
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
