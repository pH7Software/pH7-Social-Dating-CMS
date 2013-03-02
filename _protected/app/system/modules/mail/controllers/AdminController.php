<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */
namespace PH7;
use
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Url\HeaderUrl,
PH7\Framework\Mvc\Router\UriRoute;

class AdminController extends MainController
{

    public function index()
    {
        HeaderUrl::redirect(UriRoute::get('mail', 'admin', 'msglist'));
    }

    public function msgList()
    {
        $this->iTotalMails = $this->oMailModel->allMessage($this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 20);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oAllMsg = $this->oMailModel->allMessage($this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if(empty($oAllMsg)) {
            $this->displayPageNotFound(t('No messages found!'));
        } else {
            $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');

            $this->sTitle = t('Email List');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Mail Result!', '%n% Mails Result!', $this->iTotalMails);
            $this->view->inbox = $oAllMsg;
            $this->output();
        }
    }

    public function delete()
    {
        if($this->oMailModel->adminDeleteMessage($this->httpRequest->post('id', 'int') )) {
            $this->sMsg = t('The message has been sent successfully!');
        } else {
            $this->sMsg = t('The message could not be deleted because there no exist.');
        }

        HeaderUrl::redirect(UriRoute::get('mail','admin','listmsg'), $this->sMsg);
    }

    public function deleteAll()
    {
        if(!(new Framework\Security\CSRF\Token)->check('mail_action'))
         {
            $this->sMsg = Form::errorTokenMsg();
        }
        else
        {
            if(count($this->httpRequest->post('action', HttpRequest::ONLY_XSS_CLEAN)) > 0)
            {
                foreach($this->httpRequest->post('action', HttpRequest::ONLY_XSS_CLEAN) as $sAction)
                {
                    $iId = (int) $sAction;
                    $this->oMailModel->adminDeleteMessage($iId);
                }
            }
            $this->sMsg = t('The message(s) has been sent successfully!');
        }

        HeaderUrl::redirect(UriRoute::get('mail','admin','msglist'), $this->sMsg);
    }

}

