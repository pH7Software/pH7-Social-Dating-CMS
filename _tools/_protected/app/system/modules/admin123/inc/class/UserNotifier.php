<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7RuntimeException;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mailable;

class UserNotifier
{
    const APPROVED_STATUS = 1;
    const DISAPPROVED_STATUS = 2;

    const MAIL_TEMPLATE_FILE = '/tpl/mail/sys/core/moderation/content_notifier.tpl';

    /** @var Mailable */
    private $oMail;

    /** @var Templatable */
    private $oView;

    /** @var string */
    private $sEmail;

    /** @var int */
    private $iType;

    public function __construct(Mailable $oMailEngine, Templatable $oView)
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
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function send()
    {
        return $this->sendMessage();
    }

    /**
     * @return int
     *
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendMessage()
    {
        $this->oView->body = $this->getNotifierMessage();
        $sRecipientEmail = $this->getEmail();

        $sMessageHtml = $this->oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . self::MAIL_TEMPLATE_FILE,
            $sRecipientEmail
        );

        $aInfo = [
            'to' => $sRecipientEmail,
            'subject' => $this->getNotifierSubject()
        ];

        return $this->oMail->send(
            $aInfo,
            $sMessageHtml,
            Mailable::HTML_FORMAT
        );
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
     * @return int
     *
     * @throws PH7RuntimeException
     */
    private function getContentStatus()
    {
        if (empty($this->iType)) {
            throw new PH7RuntimeException('Content Status hasn\'t been set with "approvedContent()" or "disapprovedContent() method.');
        }

        return $this->iType;
    }

    /**
     * @return string
     */
    private function getNotifierSubject()
    {
        if ($this->isContentDisapproved()) {
            return UserNotifierString::getDisapprovedSubject();
        }

        return UserNotifierString::getApprovedSubject();
    }

    /**
     * @return string
     *
     * @throws Framework\File\IOException
     */
    private function getNotifierMessage()
    {
        if ($this->isContentDisapproved()) {
            return UserNotifierString::getDisapprovedMessage();
        }

        return UserNotifierString::getApprovedMessage();
    }

    /**
     * @return bool
     */
    private function isContentDisapproved()
    {
        return $this->getContentStatus() === self::DISAPPROVED_STATUS;
    }

    /**
     * @return bool
     */
    private function isValidEmail()
    {
        return !empty($this->sEmail) &&
            filter_var($this->sEmail, FILTER_VALIDATE_EMAIL) !== false;
    }
}
