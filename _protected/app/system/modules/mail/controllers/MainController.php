<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Security;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;
use stdClass;

class MainController extends Controller
{
    const EMAILS_PER_PAGE = 10;

    /** @var MailModel */
    protected $oMailModel;

    /** @var Page */
    protected $oPage;

    /** @var string */
    protected $sTitle;

    /** @var string */
    protected $sMsg;

    /** @var int */
    protected $iTotalMails;

    /** @var int */
    private $iProfileId;

    /** @var bool */
    private $bAdminLogged;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        parent::__construct();

        $this->oMailModel = new MailModel;
        $this->oPage = new Page;
        $this->iProfileId = $this->session->get('member_id');
        $this->bAdminLogged = (AdminCore::auth() && !UserCore::auth());

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->designSecurity = new Security; // Security Design Class

        $this->view->csrf_token = (new Token)->generate('mail');

        $this->view->member_id = $this->iProfileId;

        $this->addAssetFiles();
    }

    public function index()
    {
        Header::redirect(
            Uri::get('mail', 'main', 'inbox')
        );
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

        if ($this->httpRequest->getExists('id')) {
            $oMsg = $this->oMailModel->readMsg($this->iProfileId, $this->httpRequest->get('id'));

            if (empty($oMsg)) {
                $this->sTitle = t('No Messages Found!');
                $this->notFound();
            } else {
                $this->setRead($oMsg);

                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        } else {
            $this->iTotalMails = $this->oMailModel->search(
                null,
                true,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                null,
                null,
                $this->iProfileId,
                MailModel::INBOX
            );
            $this->view->total_pages = $this->oPage->getTotalPages(
                $this->iTotalMails,
                self::EMAILS_PER_PAGE
            );
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(
                null,
                false,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                $this->oPage->getFirstItem(),
                $this->oPage->getNbItemsPerPage(),
                $this->iProfileId,
                MailModel::INBOX
            );

            if (empty($oMail)) {
                $this->sTitle = t('No messages in your inbox');
                $this->notFound();
                // We modify the default error message
                $this->view->error = t("Sorry %0%, you don't have any messages in your inbox.", '<em>' . $this->session->get('member_first_name') . '</em>');
            } else {
                $this->view->msgs = $oMail;
            }

            $this->manualTplInclude('msglist.inc.tpl');
        }

        $this->output();
    }

    public function outbox()
    {
        $this->sTitle = t('MailBox : Messages Sent');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id')) {
            $oMsg = $this->oMailModel->readSentMsg($this->iProfileId, $this->httpRequest->get('id'));

            if (empty($oMsg)) {
                $this->sTitle = t('Empty!');
                $this->notFound();
            } else {
                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        } else {
            $this->iTotalMails = $this->oMailModel->search(
                null,
                true,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                null,
                null,
                $this->iProfileId,
                MailModel::OUTBOX
            );
            $this->view->total_pages = $this->oPage->getTotalPages(
                $this->iTotalMails,
                self::EMAILS_PER_PAGE
            );
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(
                null,
                false,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                $this->oPage->getFirstItem(),
                $this->oPage->getNbItemsPerPage(),
                $this->iProfileId,
                MailModel::OUTBOX
            );

            if (empty($oMail)) {
                $this->sTitle = t('Sorry!');
                $this->notFound();
                // We modify the default error message
                $this->view->error = t('No messages found.');
            } else {
                $this->view->msgs = $oMail;
            }

            $this->manualTplInclude('msglist.inc.tpl');
        }

        $this->output();
    }

    public function trash()
    {
        $this->sTitle = t('MailBox : Trash');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->getExists('id')) {
            $oMsg = $this->oMailModel->readTrashMsg($this->iProfileId, $this->httpRequest->get('id'));

            if (empty($oMsg)) {
                $this->sTitle = t('Empty!');
                $this->notFound();
            } else {
                $this->setRead($oMsg);

                $this->view->page_title = $oMsg->title . ' - ' . $this->view->page_title;
                $this->view->msg = $oMsg;
            }

            $this->manualTplInclude('msg.inc.tpl');
        } else {
            $this->iTotalMails = $this->oMailModel->search(
                null,
                true,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                null,
                null,
                $this->iProfileId,
                MailModel::TRASH
            );
            $this->view->total_pages = $this->oPage->getTotalPages(
                $this->iTotalMails,
                self::EMAILS_PER_PAGE
            );
            $this->view->current_page = $this->oPage->getCurrentPage();
            $oMail = $this->oMailModel->search(
                null,
                false,
                SearchCoreModel::SEND_DATE,
                SearchCoreModel::DESC,
                $this->oPage->getFirstItem(),
                $this->oPage->getNbItemsPerPage(),
                $this->iProfileId,
                MailModel::TRASH
            );

            if (empty($oMail)) {
                $this->sTitle = t('Sorry!');
                $this->notFound();
                // We modify the default 404 error message
                $this->view->error = t('No trashed messages were found.');
            } else {
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

        $this->iTotalMails = $this->oMailModel->search(
            $this->httpRequest->get('looking'),
            true,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            null,
            null,
            $this->iProfileId,
            $sType
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalMails,
            self::EMAILS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oSearch = $this->oMailModel->search(
            $this->httpRequest->get('looking'),
            false,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage(),
            $this->iProfileId,
            $sType
        );

        if (empty($oSearch)) {
            $this->sTitle = t("Your search didn't match any of your messages.");
            $this->notFound();
        } else {
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
        $this->bStatus = $this->oMailModel->setTo(
            $this->iProfileId,
            $this->httpRequest->post('id', 'int'),
            MailModel::TRASH_MODE
        );

        if ($this->bStatus) {
            $this->sMsg = t('Your message has been moved to your trash bin.');
        } else {
            $this->sMsg = t("Your message doesn't exist anymore in your trash bin.");
        }

        Header::redirect(
            Uri::get('mail', 'main', 'inbox'),
            $this->sMsg, $this->getStatusType()
        );
    }

    public function setTrashAll()
    {
        if (!(new Token)->check('mail_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } else {
            if (count($this->httpRequest->post('action')) > 0) {
                foreach ($this->httpRequest->post('action') as $iId) {
                    $iId = (int)$iId;
                    $this->oMailModel->setTo(
                        $this->iProfileId,
                        $iId,
                        MailModel::TRASH_MODE
                    );
                }
                $this->sMsg = t('Your message(s) has/have been moved to your trash bin.');
            }
        }

        Header::redirect(
            Uri::get('mail', 'main', 'inbox'),
            $this->sMsg
        );
    }

    public function setRestor()
    {
        $this->bStatus = $this->oMailModel->setTo(
            $this->iProfileId,
            $this->httpRequest->post('id', 'int'),
            MailModel::RESTOR_MODE
        );

        if ($this->bStatus) {
            $this->sMsg = t('Your message has been moved to your inbox.');
        } else {
            $this->sMsg = t("Your message doesn't exist anymore in your inbox.");
        }

        Header::redirect(
            Uri::get('mail', 'main', 'trash'),
            $this->sMsg, $this->getStatusType()
        );
    }

    public function setRestorAll()
    {
        if (!(new Token)->check('mail_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } else {
            if (count($this->httpRequest->post('action')) > 0) {
                foreach ($this->httpRequest->post('action') as $iId) {
                    $iId = (int)$iId;
                    $this->oMailModel->setTo(
                        $this->iProfileId,
                        $iId,
                        MailModel::RESTOR_MODE
                    );
                }
                $this->sMsg = t('Your message(s) has/have been moved to your inbox.');
            }
        }

        Header::redirect(
            Uri::get('mail', 'main', 'trash'),
            $this->sMsg
        );
    }

    public function setDelete()
    {
        $iId = $this->httpRequest->post('id', 'int');

        if ($this->bAdminLogged) {
            $this->bStatus = $this->oMailModel->adminDeleteMsg($iId);
        } else {
            $this->bStatus = $this->oMailModel->setTo(
                $this->iProfileId,
                $iId,
                MailModel::DELETE_MODE
            );
        }

        if ($this->bStatus) {
            $this->sMsg = t('Your message has been deleted successfully');
        } else {
            $this->sMsg = t("Your message doesn't exist anymore.");
        }

        $sUrl = $this->bAdminLogged ? Uri::get('mail', 'admin', 'msglist') : $this->httpRequest->previousPage();
        Header::redirect($sUrl, $this->sMsg, $this->getStatusType());
    }

    public function setDeleteAll()
    {
        if (!(new Token)->check('mail_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } else {
            if (count($this->httpRequest->post('action')) > 0) {
                foreach ($this->httpRequest->post('action') as $iId) {
                    $iId = (int)$iId;

                    if ($this->bAdminLogged) {
                        $this->oMailModel->adminDeleteMsg($iId);
                    } else {
                        $this->oMailModel->setTo(
                            $this->iProfileId,
                            $iId,
                            MailModel::DELETE_MODE
                        );
                    }
                }
                $this->sMsg = t('Your message(s) has/have been deleted successfully!');
            }
        }

        $sUrl = $this->bAdminLogged ? Uri::get('mail', 'admin', 'msglist') : $this->httpRequest->previousPage();
        Header::redirect($sUrl, $this->sMsg);
    }

    /**
     * Set a Not Found Error Message.
     *
     * @return void
     */
    private function notFound()
    {
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = t("Sorry, we weren't able to find the page you requested.") . '<br />' .
            t('Please <a href="%0%">research with different keywords</a>.',
                Uri::get('mail', 'main', 'search')
            );
    }

    /**
     * Add stylesheets and JavaScript for Mail and Form.
     *
     * @return void
     */
    private function addAssetFiles()
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'mail.css'
        );

        $this->design->addJs(
            PH7_DOT,
            PH7_STATIC . PH7_JS . 'form.js,' . PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS . 'mail.js'
        );
    }

    /**
     * Get the status type.
     *
     * @return string 'success' or 'error'
     */
    private function getStatusType()
    {
        return $this->bStatus ? Design::SUCCESS_TYPE : Design::ERROR_TYPE;
    }

    /**
     * @param stdClass $oMsg
     *
     * @return void
     */
    private function setRead(stdClass $oMsg)
    {
        if ($oMsg->status == 1) {
            $this->oMailModel->setReadMsg($oMsg->messageId);
        }
    }
}
