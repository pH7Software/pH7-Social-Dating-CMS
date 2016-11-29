<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */
namespace PH7;

use
PH7\Framework\Navigation\Page,
PH7\Framework\Url\Header,
PH7\Framework\Mvc\Router\Uri;

class MainController extends Controller
{
    protected $oMailModel, $oPage, $sMsg, $sTitle, $iTotalMails;
    private $_iProfileId, $_bAdminLogged;

    public function __construct()
    {
        parent::__construct();

        $this->oMailModel = new MailModel;
        $this->oPage = new Page;
        $this->_iProfileId = $this->session->get('member_id');
        $this->_bAdminLogged = (AdminCore::auth() && !UserCore::auth());

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->designSecurity = new Framework\Layout\Html\Security; // Security Design Class

        $this->view->csrf_token = (new Framework\Security\CSRF\Token)->generate('mail');

        $this->view->member_id = $this->_iProfileId;

        // Add Css Style Content and JavaScript for Mail and Form functions
        $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'mail.css');
        $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'form.js,' . PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS . 'mail.js');
    }

    public function index()
    {
        Header::redirect(Uri::get('mail','main','inbox'));
    }

    public function compose()
    {
        // Add JS file for the Ajax autocomplete usernames list.
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
            $oMsg = $this->oMailModel->readMsg($this->_iProfileId, $this->httpRequest->get('id'));

            if (empty($oMsg))
            {
                $this->sTitle = t('No Message Found!');
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
            $this->iTotalMails = $this->oMailModel->search(null, true, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, null, null, $this->_iProfileId, MailModel::INBOX);
            $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(null, false, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), $this->_iProfileId, MailModel::INBOX);

            if (empty($oMail))
            {
                $this->sTitle = t('No message in your inbox');
                $this->_notFound();
                // We modify the default error message
                $this->view->error = t('Sorry %0%, you do not have any messages in your inbox.', '<em>' . $this->session->get('member_first_name') . '</em>');
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
        $this->view->page_title = t('MailBox : Messages Sent');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id'))
        {
            $oMsg = $this->oMailModel->readSentMsg($this->_iProfileId, $this->httpRequest->get('id'));

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
            $this->iTotalMails = $this->oMailModel->search(null, true, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, null, null, $this->_iProfileId, MailModel::OUTBOX);
            $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(null, false, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), $this->_iProfileId, MailModel::OUTBOX);

            if (empty($oMail))
            {
                $this->sTitle = t('Sorry!');
                $this->_notFound();
                // We modify the default error message
                $this->view->error = t('No message found.');
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
            $oMsg = $this->oMailModel->readTrashMsg($this->_iProfileId, $this->httpRequest->get('id'));

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
            $this->iTotalMails = $this->oMailModel->search(null, true, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, null, null, $this->_iProfileId, MailModel::TRASH);
            $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 10);
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(null, false, SearchCoreModel::SEND_DATE, SearchCoreModel::DESC, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), $this->_iProfileId, MailModel::TRASH);

            if (empty($oMail))
            {
                $this->sTitle = t('Sorry!');
                $this->_notFound();
                // We modify the default 404 error message
                $this->view->error = t('No trash was found.');
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
        $this->sTitle = t('Mail Search - Look for a message');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function result()
    {
        $sType = $this->httpRequest->get('where');

        $this->iTotalMails = $this->oMailModel->search($this->httpRequest->get('looking'), true, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null, $this->_iProfileId, $sType);
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalMails, 10);
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oSearch = $this->oMailModel->search($this->httpRequest->get('looking'), false, $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage(), $this->_iProfileId, $sType);

        if (empty($oSearch))
        {
            $this->sTitle = t('Your search did not match any of your messages.');
            $this->_notFound();
        }
        else
        {
            $this->sTitle = t('Mail | Message - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% message found!', '%n% messages found!', $this->iTotalMails);
            $this->view->msgs = $oSearch;
        }

        $this->manualTplInclude('msglist.inc.tpl');
        $this->output();
    }

    public function setTrash()
    {
        $bStatus = $this->oMailModel->setTo($this->_iProfileId, $this->httpRequest->post('id', 'int'), 'trash');
        $this->sMsg = ($bStatus) ? t('Your message has been moved to your trash bin.') : t('Your message does not exist anymore in your trash bin.');
        $sMsgType = ($bStatus) ? 'success' : 'error';

        Header::redirect(Uri::get('mail','main','inbox'), $this->sMsg, $sMsgType);
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
                foreach ($this->httpRequest->post('action') as $iId)
                {
                    $iId = (int) $iId;
                    $this->oMailModel->setTo($this->_iProfileId, $iId, 'trash');
                }
                $this->sMsg = t('Your message(s) has/have been moved to your trash bin.');
            }
        }

        Header::redirect(Uri::get('mail','main','inbox'), $this->sMsg);
    }

    public function setRestor()
    {
        $bStatus = $this->oMailModel->setTo($this->_iProfileId, $this->httpRequest->post('id', 'int'), 'restor');
        $this->sMsg = ($bStatus) ? t('Your message has been moved to your inbox.') : t('Your message does not exist anymore in your inbox.');
        $sMsgType = ($bStatus) ? 'success' : 'error';

        Header::redirect(Uri::get('mail','main','trash'), $this->sMsg, $sMsgType);
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
                foreach ($this->httpRequest->post('action') as $iId)
                {
                    $iId = (int) $iId;
                    $this->oMailModel->setTo($this->_iProfileId, $iId, 'restor');
                }
                $this->sMsg = t('Your message(s) has/have been moved to your inbox.');
            }
        }

        Header::redirect(Uri::get('mail','main','trash'), $this->sMsg);
    }

    public function setDelete()
    {
        $iId = $this->httpRequest->post('id', 'int');

        if ($this->_bAdminLogged)
            $bStatus = $this->oMailModel->adminDeleteMsg($iId);
        else
            $bStatus = $this->oMailModel->setTo($this->_iProfileId, $iId, 'delete');

        $this->sMsg = ($bStatus) ? t('Your message has been deleted successfully') : t('Your message does not exist anymore.');
        $sMsgType = ($bStatus) ? 'success' : 'error';
        $sUrl = ($this->_bAdminLogged ? Uri::get('mail','admin','msglist') : $this->httpRequest->previousPage());
        Header::redirect($sUrl, $this->sMsg, $sMsgType);
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
                foreach ($this->httpRequest->post('action') as $iId)
                {
                    $iId = (int) $iId;
                    if ($this->_bAdminLogged)
                        $this->oMailModel->adminDeleteMsg($iId);
                    else
                        $this->oMailModel->setTo($this->_iProfileId, $iId, 'delete');
                }
                $this->sMsg = t('Your message(s) has/have been deleted successfully!');
            }
        }

        $sUrl = ($this->_bAdminLogged ? Uri::get('mail','admin','msglist') : $this->httpRequest->previousPage());
        Header::redirect($sUrl, $this->sMsg);
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
        $this->view->error = t('Sorry, we weren\'t able to find the page you requested.<br />We suggest you doing a <a href="%0%">new search</a>.', Uri::get('mail','main','search'));
    }
}
