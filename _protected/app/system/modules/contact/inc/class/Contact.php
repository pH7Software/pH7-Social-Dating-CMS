<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Str\Str;

class Contact extends Core
{
    private string $sMail;

    private string $sSubject;

    private string $sFeedbackEmail;

    private string $sPhone;

    private string $sUrl;

    /**
     * Initialize the properties of the class, then send the feedback to the admin.
     */
    public function sendMessage(): bool
    {
        $this->sFeedbackEmail = DbConfig::getSetting('feedbackEmail');
        $this->sMail = $this->httpRequest->post('mail');
        $this->sSubject = $this->httpRequest->post('subject');
        $this->sPhone = $this->httpRequest->postExists('phone') ? $this->httpRequest->post('phone') : t('No Phone');
        $this->sUrl = $this->httpRequest->postExists('website') ? $this->httpRequest->post('website') : t('No Site');

        return $this->sendEmail();
    }

    private function sendEmail(): bool
    {
        /*
         * The mail template outputs these vars as raw HTML (they carry <a> tags), so every
         * visitor-supplied value is escaped here to prevent HTML/attribute injection landing
         * in the admin's inbox. The Mail layer only escapes the envelope, not the HTML body.
         */
        $oStr = new Str();
        $sMail = escape($this->sMail);
        $sPhone = escape($this->sPhone);
        $sMailAttribute = $oStr->escapeAttribute($this->sMail);
        $sPhoneAttribute = $oStr->escapeAttribute($this->sPhone);
        $sWebsite = $this->getWebsiteHtml($oStr);

        $this->view->last_name = t('Last Name: %0%', escape($this->httpRequest->post('last_name')));
        $this->view->first_name = t('First Name: %0%', escape($this->httpRequest->post('first_name')));
        $this->view->email = t('Email: %0%', '<a href="mailto:' . $sMailAttribute . '">' . $sMail . '</a>');
        $this->view->phone = t('Phone Number: %0%', '<a href="tel:' . $sPhoneAttribute . '">' . $sPhone . '</a>');
        $this->view->website = t('Website: %0%', $sWebsite);
        $this->view->subject = t('Subject: %0%', escape($this->sSubject));
        $this->view->message = t('Message: %0%', nl2br(escape($this->httpRequest->post('message'))));

        $this->view->footer_title = t('User Information');
        $this->view->footer_content =
            '<p>' . t('User IP: %0%', escape((string)$this->design->ip(null, false))) . '</p>
             <p>' . t('User Browser info: %0%', escape((string)$this->browser->getUserAgent())) . '</p>
             <p>' . t('User come from: %0%', '<a href="' . $oStr->escapeAttribute($this->httpRequest->currentUrl()) . '">' . t('URL Page') . '</a>') . '</p>';

        $sHtmlMessage = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/contact/contact_form.tpl',
            $this->sFeedbackEmail
        );

        $aInfo = [
            'from' => $this->sMail,
            'form_name' => $this->httpRequest->post('last_name') . ' ' . $this->httpRequest->post('first_name'),
            'subject' => t('Contact Form: %0%', $this->sSubject),
            'to' => $this->sFeedbackEmail
        ];

        return (new Mail())->send($aInfo, $sHtmlMessage);
    }

    private function getWebsiteHtml(Str $oStr): string
    {
        $sEscapedUrl = escape($this->sUrl);
        if (filter_var($this->sUrl, FILTER_VALIDATE_URL) === false
            || preg_match('~^https?://~i', $this->sUrl) !== 1
        ) {
            return $sEscapedUrl;
        }

        return '<a href="' . $oStr->escapeAttribute($this->sUrl) . '" target="_blank" rel="noopener noreferrer nofollow">' . $sEscapedUrl . '</a>';
    }
}
