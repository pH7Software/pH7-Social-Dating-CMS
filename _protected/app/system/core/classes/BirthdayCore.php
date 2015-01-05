<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;
use PH7\Framework\Mail\Mail;

class BirthdayCore extends Core
{

    private static $_iTotalSent = 0;

    /**
     * Sent Birthday emails.
     *
     * @return integer Total emails sent.
     */
    public function sendMails()
    {
        $oBirths = (new BirthdayCoreModel)->get();
        $oMail = new Mail;

        foreach ($oBirths as $oBirth)
        {
            // Do not send any emails at the same time to avoid overloading the mail server.
            if (self::$_iTotalSent > 300) sleep(10);

            $this->view->content = t('Hello %0%!', $oBirth->firstName) . '<br />' .
            t("All %site_name%'s team wish you a very happy birthday!") . '<br />' .
            t('Enjoy it well and enjoy yourself!');

            $sMsgHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/mail/sys/mod/user/birthday.tpl', $oBirth->email);

            $aInfo = [
                'subject' => t('Happy Birthday %0%!', $oBirth->firstName),
                'to' => $oBirth->email
            ];

            $oMail->send($aInfo, $sMsgHtml);
            self::$_iTotalSent++;
        }
        unset($oMail, $oBirths);

        return self::$_iTotalSent;
    }

}
