<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Inc / Class
 */

namespace PH7;

use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;

class Registration extends RegistrationCore
{
    public function __construct(Templatable $oView)
    {
        parent::__construct($oView);

        $this->iActiveType = (int)DbConfig::getSetting('affActivationType');
    }

    /**
     * Send the confirmation email with registration details.
     *
     * @param array $aInfo
     * @param bool $bIsUniversalLogin
     *
     * @return self
     */
    public function sendMail(array $aInfo, $bIsUniversalLogin = false)
    {
        $this->oView->content = t('Dear %0% %1%, welcome to affiliate platform %site_name%!', $aInfo['last_name'], $aInfo['first_name']) . '<br />' .
            t('Hello %0%! We are proud to welcome you as an affiliate on our %site_name%!', $aInfo['first_name']) . '<br />' .
            $this->getEmailMsg($aInfo) .
            '<br /><span style="text-decoration:underline">' . t('Please save the following information for future reference:') . '</span><br /><em>' .
            t('Email: %0%.', $aInfo['email']) . '<br />' .
            t('Username: %0%.', $aInfo['username']) . '<br />' .
            t('Password: ****** (This field is hidden to protect against theft of your account. If you have forgotten your password, please request a new one <a href="%0%">here</a>).', Uri::get('lost-password', 'main', 'forgot', 'affiliate')) . '</em>';

        $this->oView->footer = t('You are receiving this email because we received a registration application with "%0%" email address for %site_name% (%site_url%).', $aInfo['email']) . '<br />' .
            t('If you think someone has used your email address without your knowledge to create an account on %site_name%, please contact us using our contact form available on our website.');

        $sMsgHtml = $this->oView->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/affiliate/registration.tpl', $aInfo['email']);

        $aMailInfo = [
            'to' => $aInfo['email'],
            'subject' => t('Dear %0% %1%, Welcome to our affiliate platform!', $aInfo['last_name'], $aInfo['first_name'])
        ];

        (new Mail)->send($aMailInfo, $sMsgHtml);

        return $this;
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
                $sEmailMsg = t('Please %0% to make money from today!', '<a href="' . Uri::get('affiliate', 'home', 'login') . '"><b>' . t('log in') . '</b></a>');
                break;

            case self::EMAIL_ACTIVATION:
                /** We place the text outside of Uri::get() otherwise special characters will be deleted and the parameters passed in the url will be unusable thereafter. **/
                $sActivateLink = Uri::get('affiliate', 'account', 'activate') . PH7_SH . $aData['email'] . PH7_SH . $aData['hash_validation'];
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
