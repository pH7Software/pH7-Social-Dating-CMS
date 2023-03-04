<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Spam\Spam;
use PH7\Framework\Url\Header;

class MailFormProcess extends Form
{
    private const MAX_ALLOWED_LINKS = 0;

    private UserCoreModel $oUserModel;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $oMailModel = new MailModel;

        $bIsAdmin = $this->isAdminEligible();
        $sMessage = $this->httpRequest->post('message', Http::ONLY_XSS_CLEAN);
        $sCurrentTime = $this->dateTime->get()->dateTime('Y-m-d H:i:s');
        $iTimeDelay = (int)DbConfig::getSetting('timeDelaySendMail');
        $sRecipientUsername = $this->httpRequest->post('recipient');
        $iRecipientId = $this->oUserModel->getId(null, $sRecipientUsername);
        $iSenderId = $this->getSenderId();

        if ($iSenderId === $iRecipientId) {
            \PFBC\Form::setError('form_compose_mail', t('Oops! You can not send a message to yourself.'));
        } elseif ($sRecipientUsername === PH7_ADMIN_USERNAME) {
            \PFBC\Form::setError('form_compose_mail', t('Oops! You cannot reply to administrator! If you want to contact us, please use our <a href="%0%">contact form</a>.', Uri::get('contact', 'contact', 'index')));
        } elseif (!(new ExistCoreModel)->id($iRecipientId, DbTableName::MEMBER)) {
            \PFBC\Form::setError('form_compose_mail', t('Oops! The username "%0%" does not exist.', escape(substr($this->httpRequest->post('recipient'), 0, PH7_MAX_USERNAME_LENGTH), true)));
        } elseif (!$bIsAdmin && !$oMailModel->checkWaitSend($iSenderId, $iTimeDelay, $sCurrentTime)) {
            \PFBC\Form::setError('form_compose_mail', Form::waitWriteMsg($iTimeDelay));
        } elseif (!$bIsAdmin && $oMailModel->isDuplicateContent($iSenderId, $sMessage)) {
            \PFBC\Form::setError('form_compose_mail', Form::duplicateContentMsg());
        } elseif (!$bIsAdmin && Spam::areUrls($sMessage, self::MAX_ALLOWED_LINKS)) {
            \PFBC\Form::setError('form_compose_mail', Form::tooManyUrlsMsg());
        } else {
            $mSendMsg = $oMailModel->sendMsg(
                $iSenderId,
                $iRecipientId,
                $this->httpRequest->post('title'),
                $sMessage,
                $sCurrentTime
            );

            if (false === $mSendMsg) {
                \PFBC\Form::setError(
                    'form_compose_mail',
                    t('Problem while sending the message. Please try again later.')
                );
            } else {
                if ($this->canSendEmail($iRecipientId)) {
                    $this->sendMail($iRecipientId, $mSendMsg);
                }

                Header::redirect(
                    $this->getRedirectUrl(),
                    t('Your message has been successfully sent!')
                );
            }

            unset($oMailModel);
        }
    }

    /**
     * Send notification email.
     *
     * @param int $iRecipientId
     * @param int $iMsgId
     */
    private function sendMail($iRecipientId, $iMsgId): bool
    {
        $this->view->content = t('Hello %0%!', $this->httpRequest->post('recipient')) . '<br />' .
            t('You received a new message from %0%', $this->getSenderUsername()) . '<br />' .
            '<a href="' . Uri::get('mail', 'main', 'inbox', $iMsgId) . '">' . t('Click here') . '</a>' . t('to read your message.');

        $sRecipientEmail = $this->oUserModel->getEmail($iRecipientId);

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/mail/new_msg.tpl',
            $sRecipientEmail
        );

        $aInfo = [
            'to' => $sRecipientEmail,
            'subject' => t('New private message from %0% on %site_name%', $this->getSenderFirstName())
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * @return int
     */
    private function getSenderId()
    {
        if ($this->isAdminEligible()) {
            return PH7_ADMIN_ID;
        }

        return (int)$this->session->get('member_id');
    }

    /**
     * @return string
     */
    private function getSenderUsername()
    {
        if ($this->isAdminEligible()) {
            return ucfirst(PH7_ADMIN_USERNAME);
        }

        return $this->session->get('member_username');
    }

    /**
     * @return string
     */
    private function getSenderFirstName()
    {
        if ($this->isAdminEligible()) {
            return ucfirst(PH7_ADMIN_USERNAME);
        }

        return $this->session->get('member_first_name');
    }

    private function canSendEmail($iRecipientId): bool
    {
        return $this->oUserModel->isNotification($iRecipientId, 'newMsg') &&
            !$this->oUserModel->isOnline($iRecipientId);
    }

    private function isAdminEligible(): bool
    {
        return AdminCore::auth() && !UserCore::auth() && !UserCore::isAdminLoggedAs();
    }

    private function getRedirectUrl(): string
    {
        if ($this->isAdminEligible()) {
            return Uri::get(PH7_ADMIN_MOD, 'user', 'browse');
        }

        return Uri::get('mail', 'main', 'index');
    }
}
