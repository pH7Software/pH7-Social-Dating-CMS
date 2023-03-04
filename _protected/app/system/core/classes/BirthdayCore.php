<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use stdClass;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class BirthdayCore extends Core
{
    private const MAX_BULK_EMAIL_NUMBER = 200;
    private const SLEEP_SEC = 5;

    private static int $iTotalSent = 0;

    /**
     * Sent Birthday emails.
     *
     * @return int Total emails sent.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function sendMails(): int
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
     * @throws Exception
     */
    private function sendMail(stdClass $oUser, Mailable $oMailEngine): bool
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
