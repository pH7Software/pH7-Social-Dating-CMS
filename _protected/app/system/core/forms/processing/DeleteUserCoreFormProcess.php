<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

/** For "user" and "affiliate" modules **/
class DeleteUserCoreFormProcess extends Form
{
    /** @var string */
    private $sSessPrefix;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sEmail;

    public function __construct()
    {
        parent::__construct();

        $this->sSessPrefix = $this->registry->module === 'user' ? 'member' : 'affiliate';
        $this->sUsername = $this->session->get($this->sSessPrefix . '_username');
        $this->sEmail = $this->session->get($this->sSessPrefix . '_email');
        $sTable = $this->registry->module === 'user' ? DbTableName::MEMBER : DbTableName::AFFILIATE;

        $mLogin = (new UserCoreModel)->login($this->sEmail, $this->httpRequest->post('password', Http::NO_CLEAN), $sTable);
        if ($mLogin === CredentialStatusCore::INCORRECT_PASSWORD_IN_DB) {
            \PFBC\Form::setError('form_delete_account', t('Oops! This password you entered is incorrect.'));
        } else {
            $this->session->regenerateId();
            $this->sendWarnEmail();
            $this->removeAccount();
            (new UserCore)->logout($this->session);

            $this->redirectToGoodbyePage();
        }
    }

    /**
     * Send an email to the admin saying the reason why a user wanted to delete their account.
     *
     * @return int
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function sendWarnEmail()
    {
        $sAdminEmail = DbConfig::getSetting('adminEmail');

        $sMembershipType = $this->registry->module === 'affiliate' ? t('Affiliate') : t('Member');

        $this->view->membership = t('User Type: %0%.', $sMembershipType);
        $this->view->message = nl2br($this->httpRequest->post('message'));
        $this->view->why_delete = t('Reason why the user wanted to leave: %0%', $this->httpRequest->post('why_delete'));
        $this->view->footer_title = t('User Information');
        $this->view->email = t('Email: %0%', $this->sEmail);
        $this->view->username = t('Username: %0%', $this->sUsername);
        $this->view->first_name = t('First Name: %0%', $this->session->get($this->sSessPrefix . '_first_name'));
        $this->view->sex = t('Sex: %0%', $this->session->get($this->sSessPrefix . '_sex'));
        $this->view->ip = t('User IP: %0%', $this->session->get($this->sSessPrefix . '_ip'));
        $this->view->browser_info = t('Browser info: %0%', $this->session->get($this->sSessPrefix . '_http_user_agent'));

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/core/delete_account.tpl',
            $sAdminEmail
        );

        $sMembershipName = $this->registry->module === 'user' ? t('Member') : t('Affiliate');

        /**
         * Set the details for sending the email, then send it.
         */
        $aInfo = [
            'to' => $sAdminEmail,
            'subject' => t('Unsubscribe %0% - User: %1%', $sMembershipName, $this->sUsername)
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * Remove the user/affiliate account.
     *
     * @return void
     */
    private function removeAccount()
    {
        $oUserModel = $this->registry->module === 'user' ? new UserCore : new AffiliateCore;
        $oUserModel->delete($this->session->get($this->sSessPrefix . '_id'), $this->sUsername);
        unset($oUserModel);
    }

    /**
     * Redirect the user to the goodbye (accountdeleted) page.
     *
     * @return void
     *
     * @throws Framework\File\IOException
     */
    private function redirectToGoodbyePage()
    {
        Header::redirect(
            Uri::get('user', 'main', 'accountdeleted')
        );
    }
}
