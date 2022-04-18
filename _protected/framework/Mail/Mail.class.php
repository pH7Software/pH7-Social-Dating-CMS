<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mail
 * @version          2.0
 *
 * @history          11/04/2021 - Use strict type declarations
 * @history          04/18/2022 - Moved from Swift Mailer (now discontinued) to Symfony Mailer.
 */

declare(strict_types=1);

namespace PH7\Framework\Mail;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as EmailMessage;

class Mail implements Mailable
{
    /**
     * Send an email with Symfony Mailer library engine.
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    public function send(array $aInfo, string $sContents, int $iFormatType = Mailable::ALL_FORMATS): int
    {
        /*** Default values ***/
        $sFromMail = empty($aInfo['from']) ? DbConfig::getSetting('returnEmail') : $aInfo['from'];
        $sFromName = empty($aInfo['form_name']) ? DbConfig::getSetting('emailName') : $aInfo['form_name'];

        $sToMail = empty($aInfo['to']) ? DbConfig::getSetting('adminEmail') : $aInfo['to'];
        $sToName = empty($aInfo['to_name']) ? $sToMail : $aInfo['to_name'];

        $sSubject = $aInfo['subject'];

        try {
            // Setup the mailer
            $oTransport = new SendmailTransport();
            $oMailer = new Mailer($oTransport);

            $oMessage = new EmailMessage();
            $oMessage->from(new Address(escape($sFromMail, true), escape($sFromName, true)));
            $oMessage->to(new Address(escape($sToMail, true), escape($sToName, true)));
            $oMessage->priority(EmailMessage::PRIORITY_HIGHEST);
            $oMessage->subject(escape($sSubject, true));

            if ($iFormatType === Mailable::TEXT_FORMAT || $iFormatType === Mailable::ALL_FORMATS) {
                $oMessage->text($sContents);
            }

            if ($iFormatType === Mailable::HTML_FORMAT || $iFormatType === Mailable::ALL_FORMATS) {
                $oMessage->html($sContents);
            }

            $iResult = $oMailer->send($oMessage);
        } catch (TransportExceptionInterface $e) {
            $iResult = 0;
        }

        /*
         * Check if Symfony Mailer is able to send message, otherwise we use the traditional native PHP mail() function
         * as on some hosts config, Symfony Mailer doesn't work.
         */
        if (!$iResult) {
            $aData = [
                'from' => $sFromMail,
                'to' => $sToMail,
                'subject' => $sSubject,
                'body' => $sContents
            ];
            $iResult = (int)$this->phpMail($aData);
        }

        return $iResult;
    }

    /**
     * Send an email with the native PHP mail() function in text and HTML format.
     *
     * @param array $aParams The parameters' information to send email.
     *
     * @return bool Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
     */
    protected function phpMail(array $aParams): bool
    {
        // If the email sender is empty, we define the server email.
        if (empty($aParams['from'])) {
            $aParams['from'] = $_SERVER['SERVER_ADMIN'];
        }

        /*** Headers ***/
        // To avoid the email goes in the spam folder of email client.
        $sHeaders = "From: \"{$_SERVER['HTTP_HOST']}\" <{$_SERVER['SERVER_ADMIN']}>\r\n";

        $sHeaders .= "Reply-To: <{$aParams['from']}>\r\n";
        $sHeaders .= "MIME-Version: 1.0\r\n";
        $sHeaders .= "Content-Type: text/html; charset=\"utf-8\"\r\n";

        /** Send Email ***/
        return @mail($aParams['to'], $aParams['subject'], $aParams['body'], $sHeaders);
    }
}
