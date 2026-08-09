<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
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
use PH7\HtmlToText\Convert as Html2Text;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as EmailMessage;

class Mail implements Mailable
{
    private const MAILER_DSN_ENV = 'PH7_MAILER_DSN';

    /**
     * Send an email with Symfony Mailer library engine.
     */
    public function send(array $aInfo, string $sContents, int $iFormatType = Mailable::ALL_FORMATS): bool
    {
        /*** Default values ***/
        $mFromMail = $this->getConfiguredFromAddress();
        if (!self::isValidEmailAddress($mFromMail)) {
            $mFromMail = $_SERVER['SERVER_ADMIN'] ?? null;
        }
        $mReplyToMail = empty($aInfo['from']) ? $mFromMail : $aInfo['from'];
        $mToMail = empty($aInfo['to']) ? DbConfig::getSetting('adminEmail') : $aInfo['to'];
        $mSubject = $aInfo['subject'] ?? null;

        if (!self::isValidEmailAddress($mFromMail)
            || !self::isValidEmailAddress($mReplyToMail)
            || !self::isValidEmailAddress($mToMail)
            || !self::isValidSubject($mSubject)
        ) {
            return false;
        }

        $sFromMail = trim($mFromMail);
        $sReplyToMail = trim($mReplyToMail);
        $sToMail = trim($mToMail);
        $sFromName = $this->getConfiguredFromName();
        $sFromName = is_string($sFromName) ? $sFromName : '';
        $sReplyToName = empty($aInfo['form_name']) ? $sReplyToMail : escape($aInfo['form_name'], true);
        $sReplyToName = is_string($sReplyToName) ? $sReplyToName : $sReplyToMail;
        $sToName = empty($aInfo['to_name']) ? $sToMail : escape($aInfo['to_name'], true);
        $sToName = is_string($sToName) ? $sToName : $sToMail;
        $sSubject = escape($mSubject, true);
        $sMailerDsn = $this->getMailerDsn();
        $bResult = false;

        try {
            // Setup the mailer
            $oTransport = $this->createTransport($sMailerDsn);
            $oMailer = new Mailer($oTransport);

            $oMessage = new EmailMessage();
            $oMessage->from(new Address($sFromMail, $sFromName));
            $oMessage->replyTo(new Address($sReplyToMail, $sReplyToName));
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
            $this->logTransportError($oE, $sMailerDsn !== null);
        } catch (\Throwable $oE) {
            $this->logTransportError($oE, $sMailerDsn !== null);
        }

        /*
         * Check if Symfony Mailer is able to send message, otherwise we use the traditional native PHP mail() function
         * as on some hosts config, Symfony Mailer doesn't work.
         */
        if (!$bResult && $sMailerDsn === null) {
            $aData = [
                'from' => $sFromMail,
                'reply_to' => $sReplyToMail,
                'to' => $sToMail,
                'subject' => $sSubject,
                'body' => $sContents
            ];
            $bResult = $this->phpMail($aData);
        }

        return $bResult;
    }

    protected function getMailerDsn(): ?string
    {
        $mMailerDsn = getenv(self::MAILER_DSN_ENV);
        if (!is_string($mMailerDsn) || trim($mMailerDsn) === '') {
            return null;
        }

        return trim($mMailerDsn);
    }

    protected function createTransport(?string $sMailerDsn): TransportInterface
    {
        return $sMailerDsn === null
            ? new SendmailTransport()
            : Transport::fromDsn($sMailerDsn);
    }

    protected function getConfiguredFromAddress(): mixed
    {
        return DbConfig::getSetting('returnEmail');
    }

    protected function getConfiguredFromName(): mixed
    {
        return DbConfig::getSetting('emailName');
    }

    /**
     * Send an email with the native PHP mail() function in text and HTML format.
     *
     * @param array $aParams the parameters' information to send email
     *
     * @return bool returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise
     */
    protected function phpMail(array $aParams): bool
    {
        if (!self::isValidEmailAddress($aParams['from'] ?? null)
            || !self::isValidEmailAddress($aParams['reply_to'] ?? null)
            || !self::isValidEmailAddress($aParams['to'] ?? null)
            || !self::isValidSubject($aParams['subject'] ?? null)
            || !isset($aParams['body'])
            || !is_string($aParams['body'])
        ) {
            return false;
        }

        $sServerEmail = trim($aParams['from']);
        $sReplyTo = trim($aParams['reply_to']);
        $sSiteName = preg_replace('/[\r\n"]/', '', (string)PH7_DOMAIN);
        $sSiteName = is_string($sSiteName) && $sSiteName !== '' ? $sSiteName : 'localhost';

        /*** Headers ***/
        // To avoid the email goes in the spam folder of email client.
        $sHeaders = "From: \"{$sSiteName}\" <{$sServerEmail}>\r\n";

        $sHeaders .= "Reply-To: <{$sReplyTo}>\r\n";
        $sHeaders .= "MIME-Version: 1.0\r\n";
        $sHeaders .= "Content-Type: text/html; charset=\"utf-8\"\r\n";

        /* Send Email ** */
        return @mail($aParams['to'], $aParams['subject'], $aParams['body'], $sHeaders);
    }

    protected function logTransportError(\Throwable $oException, bool $bUsesConfiguredTransport): void
    {
        $sMessage = $bUsesConfiguredTransport
            ? 'Error while sending email with the configured PH7_MAILER_DSN transport.'
            : 'Error while sending email with Symfony Mailer. ' . $oException->getMessage();

        (new Logger())->msg($sMessage);
    }

    private static function isValidEmailAddress(mixed $mAddress): bool
    {
        return is_string($mAddress)
            && filter_var(trim($mAddress), FILTER_VALIDATE_EMAIL) !== false;
    }

    private static function isValidSubject(mixed $mSubject): bool
    {
        return is_string($mSubject) && preg_match('/[\r\n]/', $mSubject) !== 1;
    }
}
