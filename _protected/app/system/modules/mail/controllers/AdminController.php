<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */
namespace PH7;

class AdminController extends MainController
{
    public function index()
    {
        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('mail', 'admin', 'msglist'));
    }

    public function msgList()
    {
        $this->iTotalMails = $this->oMailModel->search($this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oAllMsg = $this->oMailModel->search($this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if (empty($oAllMsg))
        {
            $this->displayPageNotFound(t('No messages found!'));
        }
        else
        {
            $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');

            $this->sTitle = t('Email List');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% message found!', '%n% messages found!', $this->iTotalMails);
            $this->view->msgs = $oAllMsg;

            $this->output();
        }
    }
}
