<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mail\Mail,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

/** For "user" and "affiliate" module **/
class DeleteUserCoreFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $sTable = ($this->registry->module == 'user') ? 'Members' : 'Affiliates';
        $sSessPrefix = ($this->registry->module == 'user') ? 'member' : 'affiliate';

        if ((new UserCoreModel)->login($this->session->get($sSessPrefix.'_email'), $this->httpRequest->post('password'), $sTable) === 'password_does_not_exist')
        {
            \PFBC\Form::setError('form_delete_account',t('Oops! This password you entered is incorrect.'));
        }
        else
        {
            $sUsername = $this->session->get($sSessPrefix.'_username');
            $sMembershipType = ($this->registry->module == 'affiliate') ? t('Affiliate') : t('Member');

            $this->view->membership = t('Type of Membership: %0%.', $sMembershipType);
            $this->view->message = nl2br($this->httpRequest->post('message'));
            $this->view->why_delete = t('Due to the deletion of the account: %0%', $this->httpRequest->post('why_delete'));
            $this->view->footer_title = t('Information of the user who has deleted their account');
            $this->view->email = t('Email: %0%', $this->session->get($sSessPrefix.'_email'));
            $this->view->username = t('Username: %0%', $sUsername);
            $this->view->first_name = t('First Name: %0%', $this->session->get($sSessPrefix.'_first_name'));
            $this->view->sex = t('Sex: %0%', $this->session->get($sSessPrefix.'_sex'));
            $this->view->ip = t('User IP: %0%', $this->session->get($sSessPrefix.'_ip'));
            $this->view->browser_info = t('Browser info: %0%', $this->session->get($sSessPrefix.'_http_user_agent'));

            $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_NAME . '/mail/sys/core/delete_account.tpl', DbConfig::getSetting('adminEmail'));

            $sMembershipName = ($this->registry->module == 'user') ? t('Member') : t('Affiliate');
            $aInfo = [
                'subject' => t('Unregister %0% - User: %1%', $sMembershipName, $sUsername)
            ];

            (new Mail)->send($aInfo, $sMessageHtml);

            $oUserModel = ($this->registry->module == 'user') ? new UserCore : new AffiliateCore;
            $oUserModel->delete($this->session->get($sSessPrefix.'_id'), $sUsername);
            unset($oUserModel);

            $this->session->destroy();

            Header::redirect(Uri::get('user','main','soon'), t('You delete account is successfully!'));
        }
    }

}
