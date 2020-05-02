<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use stdClass;

class BirthdayCore extends Core
{
    const MAX_BULK_EMAIL_NUMBER = 300;
    const SLEEP_SEC = 10;

    /** @var int */
    private static $iTotalSent = 0;

    /**
     * Sent Birthday emails.
     *
     * @return int Total emails sent.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function sendMails()
    {
        $oBirths = (new BirthdayCoreModel)->get();
        $oMail = new Mail;

        foreach ($oBirths as $oBirth) {
            // Do not send any emails at the same time to avoid overloading the mail server.
            if (self::$iTotalSent > self::MAX_BULK_EMAIL_NUMBER) {
                sleep(self::SLEEP_SEC);
            }

            if ($this->sendMail($oBirth, $oMail)) {
                self::$iTotalSent++;
            }
        }
        unset($oMail, $oBirths);

        return self::$iTotalSent;
    }

    /**
     * Send birthday emails to users.
     *
     * @param stdClass $oUser User data from the DB.
     * @param Mailable $oMailEngine
     *
     * @return int Number of recipients who were accepted for delivery.
     *
     * @throws \PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendMail(stdClass $oUser, Mailable $oMailEngine)
    {
        $this->view->content = t('Hi %0%!', $oUser->firstName) . '<br />' .
            t('We wish you a very Happy Birthday!') . '<br />' .
            t('Enjoy yourself and have an amazing day!');

        $sHtmlMsg = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/mail/sys/mod/user/birthday.tpl',
            $oUser->email
        );

        $aInfo = [
            'subject' => t('Happy Birthday %0%!', $oUser->firstName),
            'to' => $oUser->email
        ];

        return $oMailEngine->send($aInfo, $sHtmlMsg);
    }
}
