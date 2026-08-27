<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminController extends MainController
{
    const EMAILS_PER_PAGE = 20;

    public function index()
    {
        Header::redirect(
            Uri::get('mail', 'admin', 'msglist')
        );
    }

    public function msgList()
    {
        $this->iTotalMails = $this->oMailModel->search(
            $this->httpRequest->get('looking'),
            true,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalMails,
            self::EMAILS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oAllMsg = $this->oMailModel->search(
            $this->httpRequest->get('looking'),
            false,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->sTitle = t('Email List');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->h3_title = nt('%n% message found!', '%n% messages found!', $this->iTotalMails);

        if (empty($oAllMsg)) {
            $this->view->error = empty($this->httpRequest->get('looking'))
                ? t('No member messages yet. New conversations will appear here.')
                : t('No messages match your search. Try different keywords.');
        } else {
            $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');
            $this->view->msgs = $oAllMsg;
        }

        $this->output();
    }
}
