<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Browser;
use stdClass;

class SecurityCore
{
    /**
     * Send a Security Alert Login Attempts email.
     *
     * @param int $iMaxAttempts
     * @param int $iAttemptTime
     * @param string $sIp IP address
     * @param string $sTo Email address of the user to send the message.
     * @param Templatable $oView
     * @param string $sTable Default DbTableName::MEMBER
     *
     * @return void
     */
    public function sendLoginAttemptsExceededAlert($iMaxAttempts, $iAttemptTime, $sIp, $sTo, Templatable $oView, $sTable = DbTableName::MEMBER)
    {
        Various::checkModelTable($sTable);

        $sFirstName = $this->getUserFirstNameFromEmail($sTo, $sTable);
        $sForgotPwdLink = Uri::get('lost-password', 'main', 'forgot', Various::convertTableToMod($sTable));

        $oView->content = t('Hi %0%', $sFirstName) . '<br />' .
            t('Someone tried to login more than %0% times with the IP address: "%1%".', $iMaxAttempts, $sIp) . '<br />' .
            t('For safety and security reasons, we have blocked access to this person for a delay of %0% minutes.', $iAttemptTime) . '<br /><ol><li>' .
            t('If it was you who tried to login to your account, we suggest to <a href="%1%">request a new password</a> in %0% minutes.', $iAttemptTime, $sForgotPwdLink) . '</li><li>' .
            t("If you don't know the person who made the login attempts, you should be very careful and change your password to a new complex one.") . '</li></ol><br /><hr />' .
            t('Have a nice day!');

        $sMessageHtml = $oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/core/alert_login_attempt.tpl',
            $sTo
        );

        $aInfo = [
            'to' => $sTo,
            'subject' => t('Security Alert: Login Attempts - %site_name%')
        ];

        (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * Get the first name of the user from their email.
     *
     * @param string $sEmail
     * @param string $sTable
     *
     * @return string
     */
    private function getUserFirstNameFromEmail($sEmail, $sTable)
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = $oUserModel->getId($sEmail, null, $sTable);
        $sFirstName = $oUserModel->getFirstName($iProfileId, $sTable);
        unset($oUserModel);

        return $sFirstName;
    }

    /**
     * @param string $sLocationName
     * @param stdClass $oUserData
     * @param Browser $oBrowser
     * @param Templatable $oView
     *
     * @return void
     */
    public static function sendSuspiciousLocationAlert($sLocationName, stdClass $oUserData, Browser $oBrowser, Templatable $oView)
    {
        $oView->content = t('Hi %0%', $oUserData->firstName) . '<br />' .
            t('Your account "%0% has just been logged-in from a different location than usual.', $oUserData->username) . '<br />' .
            t("We are sending this notification in case this wasn't done by you.") . '<br />' .
            '<strong>' . t('Details:') . '</strong><br /><ol><li>' .
            t('<strong>Location:</strong> %0% (determined from IP address).', $sLocationName) . '<li></li>' .
            t('<strong>Browser:</strong> %0%', $oBrowser->getUserAgent()) . '</li></ol><br /><hr />' .
            t("If this wasn't you, please <a href='%0%'>login</a> immediately to change your password.", Uri::get('user', 'main', 'login')) . '<br />' .
            t('Have a great day!');

        $sMessageHtml = $oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/core/alert_suspicious_location.tpl',
            $oUserData->email
        );

        $aInfo = [
            'to' => $oUserData->email,
            'subject' => t('Foreign login to your account - %site_name%')
        ];

        (new Mail)->send($aInfo, $sMessageHtml);
    }
}
