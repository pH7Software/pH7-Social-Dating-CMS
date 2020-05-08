<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;

/**
 * @abstract
 */
abstract class RegistrationCore
{
    const REFERENCE_VAR_NAME = 'join_ref';

    const NO_ACTIVATION = 1;
    const EMAIL_ACTIVATION = 2;
    const MANUAL_ACTIVATION = 3;
    const SMS_ACTIVATION = 4;

    /**
     * @internal Set protected visibility because this attribute is used in child classes.
     */
    /** @var int */
    protected $iActiveType;

    /** @var Templatable */
    protected $oView;

    public function __construct(Templatable $oView)
    {
        $this->oView = $oView;
        $this->iActiveType = (int)DbConfig::getSetting('userActivationType');
    }

    /**
     * Send the confirmation email with registration details.
     *
     * @param array $aInfo
     * @param bool $bIsUniversalLogin
     *
     * @return self
     *
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function sendMail(array $aInfo, $bIsUniversalLogin = false)
    {
        if ($bIsUniversalLogin) {
            $sPwdMsg = t('Password: %0% (please change it next time you login).', $aInfo['password']);
        } else {
            $sPwdMsg = t('Password: ****** (hidden to protect against theft of your account. If you have forgotten your password, please request a new one <a href="%0%">here</a>).', Uri::get('lost-password', 'main', 'forgot', 'user'));
        }

        $this->oView->content = t('Welcome to %site_name%, %0%!', $aInfo['first_name']) . '<br />' .
            t('Hi %0%! We are proud to welcome you as a member of %site_name%!', $aInfo['first_name']) . '<br />' .
            $this->getEmailMsg($aInfo) . '<br />' .
            '<br /><span style="text-decoration:underline">' . t('Please save the following information for future reference:') . '</span><br /><em>' .
            t('Email: %0%.', $aInfo['email']) . '<br />' .
            t('Username: %0%.', $aInfo['username']) . '<br />' .
            $sPwdMsg . '</em>';

        $this->oView->footer = t('You are receiving this email because we received a registration application with "%0%" email address for %site_name% (%site_url%).', $aInfo['email']) . '<br />' .
            t('If you think someone has used your email address without your knowledge to create an account on %site_name%, please contact us using our contact form available on our website.');

        $sTplName = defined('PH7_TPL_MAIL_NAME') ? PH7_TPL_MAIL_NAME : PH7_DEFAULT_THEME;
        $sMsgHtml = $this->oView->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . $sTplName . '/tpl/mail/sys/mod/user/account_registration.tpl', $aInfo['email']);

        $aMailInfo = [
            'to' => $aInfo['email'],
            'subject' => t('Hello %0%, Welcome to %site_name%!', $aInfo['first_name'])
        ];

        (new Mail)->send($aMailInfo, $sMsgHtml);

        return $this;
    }

    /**
     * Get the registration status message.
     *
     * @return string
     */
    public function getMsg()
    {
        switch ($this->iActiveType) {
            case self::NO_ACTIVATION:
                $sMsg = t('Your account has just been created. You can now login!');
                break;

            case self::EMAIL_ACTIVATION:
                $sMsg = t('Please activate your account by clicking the activation link you received by email. If you can not find the email, please look in your SPAM FOLDER and mark as not spam.');
                break;

            case self::MANUAL_ACTIVATION:
                $sMsg = t('Your account must be approved by an administrator. You will receive an email of any decision.');
                break;

            case self::SMS_ACTIVATION:
                $sMsg = t('You have been successfully registered!');
                break;

            default:
                $sMsg = t('You have been successfully registered!');
        }

        return $sMsg;
    }

    /**
     * The the email message to send.
     *
     * @param array $aData
     *
     * @return string
     */
    private function getEmailMsg(array $aData)
    {
        switch ($this->iActiveType) {
            case self::NO_ACTIVATION:
                $sEmailMsg = t('Please %0% to meet new people from today!', '<a href="' . Uri::get('user', 'main', 'login') . '"><b>' . t('log in') . '</b></a>');
                break;

            case self::EMAIL_ACTIVATION:
                /* We place the text outside of "Uri::get()", otherwise special characters will be deleted and the params passed in the URL will be unusable thereafter */
                $sActivateLink = Uri::get('user', 'account', 'activate') . PH7_SH . $aData['email'] . PH7_SH . $aData['hash_validation'];
                $sEmailMsg = t('Activation link: %0%.', '<a href="' . $sActivateLink . '">' . $sActivateLink . '</a>');
                break;

            case self::MANUAL_ACTIVATION:
                $sEmailMsg = t('Caution! Your account is not activated yet. You will receive an email of any decision.');
                break;

            default:
                $sEmailMsg = '';
        }

        return $sEmailMsg;
    }
}
