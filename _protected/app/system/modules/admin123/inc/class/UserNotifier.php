<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Router\Uri;

class UserNotifier
{
    const APPROVED_STATUS = 1;
    const DISAPPROVED_STATUS = 2;

    const MAIL_TEMPLATE_FILE = '/tpl/mail/sys/core/moderation/content_notifier.tpl';

    /** @var Mail */
    private $oMail;

    /** @var PH7Tpl */
    private $oView;

    /** @var string */
    private $sEmail;

    /** @var int */
    private $iType;

    public function __construct(Mailable $oMailEngine, PH7Tpl $oView)
    {
        $this->oMail = $oMailEngine;
        $this->oView = $oView;
    }

    /**
     * @param string $sUserEmail
     *
     * @return self
     */
    public function setUserEmail($sUserEmail)
    {
        $this->sEmail = $sUserEmail;

        return $this;
    }

    /**
     * @return self
     */
    public function approvedContent()
    {
        $this->iType = self::APPROVED_STATUS;

        return $this;
    }

    /**
     * @return self
     */
    public function disapprovedContent()
    {
        $this->iType = self::DISAPPROVED_STATUS;

        return $this;
    }

    /**
     * @return int Number of recipients who were accepted for delivery.
     *
     * @throws Framework\File\Exception
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function send()
    {
        return $this->sendMessage();
    }

    /**
     * @return int
     *
     * @throws Framework\File\Exception
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendMessage()
    {
        $this->oView->content = $this->getNotifierMessage();
        $sRecipientEmail = $this->getEmail();

        $sMessageHtml = $this->oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . self::MAIL_TEMPLATE_FILE,
            $sRecipientEmail
        );

        $aInfo = [
            'to' => $sRecipientEmail,
            'subject' => $this->getNotifierSubject()
        ];

        return $this->oMail->send($aInfo, $sMessageHtml);
    }

    /**
     * @return string
     *
     * @throws InvalidEmailException
     */
    private function getEmail()
    {
        if (!$this->isValidEmail()) {
            throw new InvalidEmailException(
                t('"%0%" is an invalid email address.', $this->sEmail)
            );
        }

        return $this->sEmail;
    }

    /**
     * @return string
     */
    private function getNotifierSubject()
    {
        if ($this->iType === self::DISAPPROVED_STATUS) {
            return $this->getDisapprovedSubject();
        }

        return $this->getApprovedSubject();
    }

    /**
     * @return string
     *
     * @throws Framework\File\Exception
     */
    private function getNotifierMessage()
    {
        if ($this->iType === self::DISAPPROVED_STATUS) {
            return $this->getDisapprovedMessage();
        }

        return $this->getApprovedMessage();
    }

    /**
     * @return bool
     */
    private function isValidEmail()
    {
        return !empty($this->sEmail) && filter_var($this->sEmail, FILTER_VALIDATE_EMAIL) !== false;

    }

    /**
     * @return string
     */
    private function getApprovedSubject()
    {
        return t('Your content has been approved!');
    }

    /**
     * @return string
     */
    private function getDisapprovedSubject()
    {
        return t('Your content has been disapproved :(');
    }

    /**
     * @return string
     */
    private function getApprovedMessage()
    {
        $sMsg = t('Congratulation! The content you recently posted at <a href="%site_url%">%site_name%</a> has been successfully approved by the team.');
        $sMsg .= '<br />';
        $sMsg .= t('Other users will now enjoy what you posted and thanks you, our online service gets better! :)');

        return $sMsg;
    }

    /**
     * @return string
     *
     * @throws Framework\File\Exception
     */
    private function getDisapprovedMessage()
    {
        $sTermsUrl = Uri::get('page', 'main', 'terms');

        $sMsg = t('Your content you recently posted at <a href="%site_url%">%site_name%</a> has unfortunately been disapproved by our moderation team.');
        $sMsg .= '<br />';
        $sMsg .= t('Indeed, it looks like it does not respect our <a href="%0%">terms of service</a>.', $sTermsUrl);
        $sMsg .= '<br />';
        $sMsg .= t('Please feel free to post again a content at any time as long as it respects our <a href="%0%">terms of service</a>', $sTermsUrl);

        return $sMsg;
    }
}
