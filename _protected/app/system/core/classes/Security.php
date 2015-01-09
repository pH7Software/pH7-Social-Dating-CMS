<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use
PH7\Framework\Mail\Mail,
PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Mvc\Model\Engine\Util\Various;

class Security
{

    /**
     * Send a Security Alert Login Attempts email.
     *
     * @param integer $iMaxAttempts
     * @param integer $iAttemptTime
     * @param string $sIp IP address
     * @param string $sTo Email address to send the message.
     * @param object \PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl $oView
     * @param string $sTable Default 'Members'
     * @return void
     */
    public function sendAlertLoginAttemptsExceeded($iMaxAttempts, $iAttemptTime, $sIp, $sTo, PH7Tpl $oView, $sTable = 'Members')
    {
        Various::checkModelTable($sTable);

        $sForgotPwdLink = Uri::get('lost-password', 'main', 'forgot', Various::convertTableToMod($sTable));

        $oView->content = t('Dear, %0%', (new UserCoreModel)->getUsername($sTo, $sTable)) . '<br />' .
        t('Somebody tried to connect more %0% times with the IP address: "%1%".', $iMaxAttempts, $sIp) . '<br />' .
        t('For safety reasons we have blocked access to this person for a delay of %1% minutes.', $iAttemptTime) . '<br /><ol><li>' .
        t('If it is you who have made ​​the connection attempts, we suggest you request a new password <a href="%0%">here</a> in %1% minutes.', $iAttemptTime, $sForgotPwdLink) . '</li><li>' .
        t('If you do not know the person who made ​​the connection attempts, you should be very careful and change your password to a password more complicated.') . '<br />' .
        t('We also recommend that you change the password for your mailbox, because it is in this box email we send a potential new password in case you forget.') . '</li></ol><br /><hr />' .
        t('Have a nice day!');

        $sMessageHtml = $oView->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_NAME . '/mail/sys/core/alert_login_attempt.tpl', $sTo);

        $aInfo = [
            'to' => $sTo,
            'subject' => t('Security Alert : Login Attempts - %site_name%')
        ];

        (new Mail)->send($aInfo, $sMessageHtml);
    }

}
