<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Inc / Class
 */

namespace PH7;

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;

class Contact extends Core
{
    /** @var string */
    private $sMail;

    /** @var string */
    private $sSubject;

    /** @var string */
    private $sFeedbackEmail;

    /** @var string */
    private $sPhone;

    /** @var string */
    private $sUrl;

    /**
     * Initialize the properties of the class, then send the feedback to the admin.
     *
     * @return integer Number of recipients who were accepted for delivery.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function sendMessage()
    {
        $this->sFeedbackEmail = DbConfig::getSetting('feedbackEmail');
        $this->sMail = $this->httpRequest->post('mail');
        $this->sSubject = $this->httpRequest->post('subject');
        $this->sPhone = $this->httpRequest->postExists('phone') ? $this->httpRequest->post('phone') : t('No Phone');
        $this->sUrl = $this->httpRequest->postExists('website') ? $this->httpRequest->post('website') : t('No Site');

        return $this->goMail();
    }

    /**
     * Send the email.
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    private function goMail()
    {
        $this->view->last_name = t('Last Name: %0%', $this->httpRequest->post('last_name'));
        $this->view->first_name = t('First Name: %0%', $this->httpRequest->post('first_name'));
        $this->view->email = t('Email: %0%', '<a href="mailto:' . $this->sMail . '">' . $this->sMail . '</a>');
        $this->view->phone = t('Phone Number: %0%', '<a href="tel:' . $this->sPhone . '">' . $this->sPhone . '</a>');
        $this->view->website = t('Website: %0%', '<a href="' . $this->sUrl . '" target="_blank">' . $this->sUrl . '</a>');
        $this->view->subject = t('Subject: %0%', $this->sSubject);
        $this->view->message = t('Message: %0%', nl2br($this->httpRequest->post('message')));

        $this->view->footer_title = t('User Information');
        $this->view->footer_content =
            '<p>' . t('User IP: %0%', $this->design->ip(null, false)) . '</p>
             <p>' . t('User Browser info: %0%', $this->browser->getUserAgent()) . '</p>
             <p>' . t('User come from: %0%', '<a href="' . $this->httpRequest->currentUrl() . '">' . t('URL Page') . '</a>') . '</p>';

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

        return (new Mail)->send($aInfo, $sHtmlMessage);
    }
}
