<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */
namespace PH7;

use
PH7\Framework\Navigation\Page,
PH7\Framework\Url\HeaderUrl,
PH7\Framework\Mvc\Router\UriRoute;

class MainController extends Controller
{

    protected $oMailModel, $oPage, $sMsg, $sTitle, $iTotalMails;
    private $_bAdminLogged;

    public function __construct()
    {
        parent::__construct();

        $this->oMailModel = new MailModel;
        $this->oPage = new Page;
        $this->_bAdminLogged = (AdminCore::auth() && !UserCore::auth());
        $this->view->dateTime = $this->dateTime;

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->designSecurity = new Framework\Layout\Html\Security; // Security Design Class

        $this->view->csrf_token = (new Framework\Security\CSRF\Token)->generate('mail');

        $this->view->member_id = $this->session->get('member_id');

        // Adding Css Style Content and JavaScript for Mail and Form
        $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_CSS, 'mail.css');
        $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'form.js,' . PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_JS . 'mail.js');
    }

    public function index()
    {
        HeaderUrl::redirect(UriRoute::get('mail','main','inbox'));
    }

    public function compose()
    {
        // Added JS file for the Ajax autocomplete usernames list.
        $this->design->addJs(PH7_STATIC . PH7_JS, 'autocompleteUsername.js');
        $this->view->page_title = t('MailBox : Compose a new message');
        $this->view->h2_title = t('Compose a new message');
        $this->output();
    }

    public function inbox()
    {
        /** Default title **/
        $this->sTitle = t('MailBox : Inbox');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id'))
        {
            $oMsg = $this->oMailModel->readMsg($this->session->get('member_id'), $this->httpRequest->get('id'));

            if (empty($oMsg))
            {
                $this->sTitle = t('Not Found Message!');
                $this->_notFound();
            }
            else
            {
                if ($oMsg->status == 1) $this->oMailModel->setReadMsg($oMsg->messageId);
                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        }
        else
        {
            $this->view->total_pages = $this->oPage->getTotalPages($this->oMailModel->totalMsg($this->session->get('member_id')), 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->readMsgs($this->session->get('member_id'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

            if (empty($oMail))
            {
                $this->sTitle = t('Empty Message!');
                $this->_notFound();
                // We modified the default error message.
                $this->view->error = t('Sorry %0%, you do not have any messages.', '<em>' . $this->session->get('member_first_name') . '</em>');
            }
            else
            {
                $this->view->msgs = $oMail;
            }

            $this->manualTplInclude('msglist.inc.tpl');
        }

        $this->output();
    }

    public function outbox()
    {
        $this->view->page_title = t('MailBox : Outbox');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id'))
        {
            $oMsg = $this->oMailModel->readSentMsg($this->session->get('member_id'), $this->httpRequest->get('id'));

            if (empty($oMsg))
            {
                $this->sTitle = t('Empty!');
                $this->_notFound();
            }
            else
            {
                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        }
        else
        {
            $this->view->total_pages = $this->oPage->getTotalPages($this->oMailModel->totalSentMsg($this->session->get('member_id')), 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->readSentMsgs($this->session->get('member_id'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

            if (empty($oMail))
            {
                $this->sTitle = t('Empty Message!');
                $this->_notFound();
                // We modified the default error message.
                $this->view->error = t('Empty message.');
            }
            else
            {
                $this->view->msgs = $oMail;
            }

            $this->manualTplInclude('msglist.inc.tpl');
        }

        $this->output();
    }

    public function trash()
    {
        $this->view->page_title = t('MailBox : Trash');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id'))
        {
            $oMsg = $this->oMailModel->readTrashMsg($this->session->get('member_id'), $this->httpRequest->get('id'));

            if (empty($oMsg))
            {
                $this->sTitle = t('Empty!');
                $this->_notFound();
            }
            else
            {
                if ($oMsg->status == 1) $this->oMailModel->setReadMsg($oMsg->messageId);
                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        }
        else
        {
            $this->view->total_pages = $this->oPage->getTotalPages($this->oMailModel->totalTrashMsg($this->session->get('member_id')), 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->readTrashMsgs($this->session->get('member_id'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

            if (empty($oMail))
            {
                $this->sTitle = t('Empty Message!');
                $this->_notFound();
                // We modified the default error message.
                $this->view->error = t('You do not have any messages in your Trash.');
            }
            else
            {
                $this->view->msgs = $oMail;
            }

            $this->manualTplInclude('msglist.inc.tpl');
        }

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Search Mail - Looking a new Message');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function result()
    {
        $sMailModelMethod = ($this->httpRequest->get('where') == 'outbox') ? 'searchSentMsg' : 'searchMsg';
        $this->iTotalMails = $this->oMailModel->$sMailModelMethod($this->session->get('member_id'), $this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oSearch = $this->oMailModel->$sMailModelMethod($this->session->get('member_id'), $this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        if (empty($oSearch))
        {
            $this->sTitle = t('Search Not Found!');
            $this->_notFound();
        }
        else
        {
            $this->sTitle = t('Email | Message - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Mail Result!', '%n% Mails Result!', $this->iTotalMails);
            $this->view->msgs = $oSearch;
        }

        $this->manualTplInclude('msglist.inc.tpl');
        $this->output();
    }

    public function setTrash()
    {
        $bStatus = $this->oMailModel->setTo($this->session->get('member_id'), $this->httpRequest->post('id', 'int'), 'trash');
        $this->sMsg = ($bStatus) ? t('Your message has been moved to your Trash.') : t('Your message could not be moved to Trash because there no exist.');
        $sMsgType = ($bStatus) ? 'success' : 'error';

        HeaderUrl::redirect(UriRoute::get('mail','main','inbox'), $this->sMsg, $sMsgType);
    }

    public function setTrashAll()
    {
        if (!(new Framework\Security\CSRF\Token)->check('mail_action'))
        {
            $this->sMsg = Form::errorTokenMsg();
        }
        else
        {
            if (count($this->httpRequest->post('action')) > 0)
            {
                foreach ($this->httpRequest->post('action') as $sAction)
                {
                    $iId = (int) $sAction;
                    $this->oMailModel->setTo($this->session->get('member_id'), $iId, 'trash');
                }
                $this->sMsg = t('Your message(s) has been moved to your Trash.');
            }
        }

        HeaderUrl::redirect(UriRoute::get('mail','main','inbox'), $this->sMsg);
    }

    public function setRestor()
    {
        $bStatus = $this->oMailModel->setTo($this->session->get('member_id'), $this->httpRequest->post('id', 'int'), 'restor');
        $this->sMsg = ($bStatus) ? t('Your message has been moved to your Inbox.') : t('Your message could not be moved to Trash because there no exist.');
        $sMsgType = ($bStatus) ? 'success' : 'error';

        HeaderUrl::redirect(UriRoute::get('mail','main','trash'), $this->sMsg, $sMsgType);
    }

    public function setRestorAll()
    {
        if (!(new Framework\Security\CSRF\Token)->check('mail_action'))
        {
            $this->sMsg = Form::errorTokenMsg();
        }
        else
        {
            if (count($this->httpRequest->post('action')) > 0)
            {
                foreach ($this->httpRequest->post('action') as $sAction)
                {
                    $iId = (int) $sAction;
                    $this->oMailModel->setTo($this->session->get('member_id'), $iId, 'restor');
                }
                $this->sMsg = t('Your message(s) has been moved to your Inbox.');
            }
        }

        HeaderUrl::redirect(UriRoute::get('mail','main','trash'), $this->sMsg);
    }

    public function setDelete()
    {
        $iId = $this->httpRequest->post('id', 'int');

        if ($this->_bAdminLogged)
            $bStatus = $this->oMailModel->adminDeleteMsg($iId);
        else
            $bStatus = $this->oMailModel->setTo($this->session->get('member_id'), $iId, 'delete');

        $this->sMsg = ($bStatus) ? t('Your message has been deleted successfully!') : t('Your message could not be deleted because there no exist.');
        $sMsgType = ($bStatus) ? 'success' : 'error';
        $sUrl = ($this->_bAdminLogged) ? UriRoute::get('mail','admin','msglist') : UriRoute::get('mail','main','trash');
        HeaderUrl::redirect($sUrl, $this->sMsg, $sMsgType);
    }

    public function setDeleteAll()
    {
        if (!(new Framework\Security\CSRF\Token)->check('mail_action'))
        {
            $this->sMsg = Form::errorTokenMsg();
        }
        else
        {
            if (count($this->httpRequest->post('action')) > 0)
            {
                foreach ($this->httpRequest->post('action') as $sAction)
                {
                    $iId = (int) $sAction;
                    if ($this->_bAdminLogged)
                        $this->oMailModel->adminDeleteMsg($iId);
                    else
                        $this->oMailModel->setTo($this->session->get('member_id'), $iId, 'delete');
                }
                $this->sMsg = t('Your message(s) has been deleted successfully!');
            }
        }

        $sUrl = ($this->_bAdminLogged) ? UriRoute::get('mail','admin','msglist') : UriRoute::get('mail','main','trash');
        HeaderUrl::redirect($sUrl, $this->sMsg);
    }

    /**
     * Set a Not Found Error Message.
     *
     * @access private
     * @return void
     */
    private function _notFound()
    {
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = t('Sorry, we weren\'t able to find the page you requested.<br />We suggest you <a href="%0%">make a new search</a>.', UriRoute::get('mail','main','search'));
    }

    public function __destruct()
    {
        unset($this->oMailModel, $this->oPage, $this->sMsg, $this->sTitle, $this->iTotalMails);
    }

}
