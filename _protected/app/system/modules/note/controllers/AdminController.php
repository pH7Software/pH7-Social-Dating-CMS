<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminController extends MainController
{
    const POSTS_PER_PAGE = 10;

    public function index()
    {
        Header::redirect(
            Uri::get('note', 'main', 'index'),
            t('Welcome to the Note administrator mode.')
        );
    }

    public function unmoderated()
    {
        $this->view->page_title = $this->view->h2_title = t('Notes Moderation');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oNoteModel->totalPosts(0), self::POSTS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();
        $oPosts = $this->oNoteModel->getPosts(
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage(),
            SearchCoreModel::CREATED,
            0
        );
        $this->view->posts = $oPosts;
        $this->setMenuVars();
        $this->output();
    }

    public function approved()
    {
        $iNoteId = $this->httpRequest->post('note_id', 'int');

        if (isset($iNoteId) && $this->oNoteModel->approved($iNoteId)) {
            Note::clearCache();
            $sMsg = t('The Note has been approved!');
        } else {
            $sMsg = t('Oops! The Note could not be approved!');
        }

        Header::redirect(
            Uri::get('note', 'admin', 'unmoderated'),
            $sMsg
        );
    }

    public function disapproved()
    {
        $iNoteId = $this->httpRequest->post('note_id', 'int');

        if (isset($iNoteId) && $this->oNoteModel->approved($iNoteId, 0)) {
            Note::clearCache();
            $sMsg = t('The Note has been approved!');
        } else {
            $sMsg = t('Oops! The Note could not be approved!');
        }

        Header::redirect(
            Uri::get('note', 'main', 'index'),
            $sMsg
        );
    }
}
