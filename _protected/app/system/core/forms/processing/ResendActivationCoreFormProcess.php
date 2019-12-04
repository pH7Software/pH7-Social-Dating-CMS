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
use PH7\Framework\Mvc\Router\Uri;
use stdClass;

/** For "user" and "affiliate" module **/
class ResendActivationCoreFormProcess extends Form
{
    public function __construct($sTable)
    {
        parent::__construct();

        $sMail = $this->httpRequest->post('mail');

        if (!(new ExistsCoreModel)->email($sMail, $sTable)) {
            \PFBC\Form::setError(
                'form_resend_activation',
                t('Oops, this "%0%" is not associated with any %site_name% account. Please, make sure that you entered the e-mail address used in creating your account.', escape(substr($sMail, 0, PH7_MAX_EMAIL_LENGTH)))
            );
        } else {
            if (!$mHash = (new UserCoreModel)->getHashValidation($sMail)) {
                \PFBC\Form::setError('form_resend_activation', t('Oops! Your account is already activated.'));
            } else {
                $iRet = $this->sendMail($mHash, $sTable);

                if ($iRet) {
                    \PFBC\Form::setSuccess('form_resend_activation', t('Your activation link has been emailed to you.'));
                } else {
                    \PFBC\Form::setError('form_resend_activation', Form::errorSendingEmail());
                }
            }
        }
    }

    /**
     * Send the confirmation email.
     *
     * @param stdClass $oHash User data from the DB.
     * @param string $sTable Table name.
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    protected function sendMail(stdClass $oHash, $sTable)
    {
        $sMod = ($sTable === DbTableName::AFFILIATE) ? 'affiliate' : 'user';
        $sActivateLink = Uri::get($sMod, 'account', 'activate') . PH7_SH . $oHash->email . PH7_SH . $oHash->hashValidation;

        $this->view->content = t('Welcome to %site_name%, %0%!', $oHash->firstName) . '<br />' .
            t('Hi %0%! We are proud to welcome you as a member of %site_name%!', $oHash->firstName) . '<br />' .
            t('Your activation link is <em>"%0%"</em>.', '<a href="' . $sActivateLink . '">' . $sActivateLink . '</a>') . '<br />' .
            t('Please save the following information for future reference:') . '<br /><em>' .
            t('Email: %0%.', $oHash->email) . '<br />' .
            t('Username: %0%.', $oHash->username) . '<br />' .
            t('Password: ***** (this field is hidden to protect against theft of your account).') . '</em>';

        $this->view->footer = t('You are receiving this email because we received a registration application with "%0%" email address for %site_name% (%site_url%).', $oHash->email) . '<br />' .
            t('If you think someone has used your email address without your knowledge to create an account on %site_name%, please contact us using our contact form available on our website.');

        $sHtmlMessage = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/core/resend_activation.tpl', $oHash->email);

        $aInfo = [
            'to' => $oHash->email,
            'subject' => t('Your new password - %site_name%')
        ];

        return (new Mail)->send($aInfo, $sHtmlMessage);
    }
}
