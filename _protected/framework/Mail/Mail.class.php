<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mail
 * @version          2.0
 *
 * @history          11/04/2021 - Use strict type declarations
 * @history          04/18/2022 - Moved from Swift Mailer (now discontinued) to Symfony Mailer.
 */

declare(strict_types=1);

namespace PH7\Framework\Mail;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\Logger;
use PH7\Framework\Mvc\Model\DbConfig;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mime\Address;
use PH7\HtmlToText\Convert as Html2Text;
use Symfony\Component\Mime\Email as EmailMessage;

class Mail implements Mailable
{
    /**
     * Send an email with Symfony Mailer library engine.
     */
    public function send(array $aInfo, string $sContents, int $iFormatType = Mailable::ALL_FORMATS): bool
    {
        /*** Default values ***/
        $sFromMail = empty($aInfo['from']) ? DbConfig::getSetting('returnEmail') : escape($aInfo['from'], true);
        $sFromName = empty($aInfo['form_name']) ? DbConfig::getSetting('emailName') : escape($aInfo['form_name'], true);
        $sToMail = empty($aInfo['to']) ? DbConfig::getSetting('adminEmail') : escape($aInfo['to'], true);
        $sToName = empty($aInfo['to_name']) ? $sToMail : escape($aInfo['to_name'], true);

        $sSubject = escape($aInfo['subject'], true);

        try {
            // Setup the mailer
            $oTransport = new SendmailTransport();
            $oMailer = new Mailer($oTransport);

            $oMessage = new EmailMessage();
            $oMessage->from(new Address($sFromMail, $sFromName));
            $oMessage->to(new Address($sToMail, $sToName));
            $oMessage->priority(EmailMessage::PRIORITY_HIGHEST);
            $oMessage->subject($sSubject);

            if ($iFormatType === Mailable::TEXT_FORMAT || $iFormatType === Mailable::ALL_FORMATS) {
                $html2Text = new Html2Text($sContents);
                $oMessage->text($html2Text->getText());
            }

            if ($iFormatType === Mailable::HTML_FORMAT || $iFormatType === Mailable::ALL_FORMATS) {
                $oMessage->html($sContents);
            }

            $oMailer->send($oMessage);
            $bResult = true;
        } catch (TransportExceptionInterface $oE) {
            (new Logger())->msg('Error while sending email with Symfony Mailer. ' . $oE->getMessage());
            $bResult = false;
        }

        /*
         * Check if Symfony Mailer is able to send message, otherwise we use the traditional native PHP mail() function
         * as on some hosts config, Symfony Mailer doesn't work.
         */
        if (!$bResult) {
            $aData = [
                'from' => $sFromMail,
                'to' => $sToMail,
                'subject' => $sSubject,
                'body' => $sContents
            ];
            $bResult = $this->phpMail($aData);
        }

        return $bResult;
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
