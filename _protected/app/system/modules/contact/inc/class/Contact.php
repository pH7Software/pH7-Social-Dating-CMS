<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Inc / Class
 */
namespace PH7;
use PH7\Framework\Mvc\Model\DbConfig, PH7\Framework\Mail\Mail;

class Contact extends Core
{

    public function sendMessage()
    {
        $sFeedbackEmail = DbConfig::getSetting('feedbackEmail');

        $sPhoneVal = ($this->httpRequest->postExists('phone')) ? $this->httpRequest->post('phone') : t('No Phone');
        $sWebsiteVal = ($this->httpRequest->postExists('website')) ? $this->httpRequest->post('website') : t('No Site');

        $this->view->last_name = t('Last Name: %0%', $this->httpRequest->post('last_name'));
        $this->view->first_name = t('First Name: %0%', $this->httpRequest->post('first_name'));
        $this->view->email = t('Email: %0%', $this->httpRequest->post('mail'));
        $this->view->phone = t('Phone Number: %0%', $sPhoneVal);
        $this->view->website = t('Website: %0%', $sWebsiteVal);
        $this->view->subject = t('Subject: %0%', $this->httpRequest->post('subject'));
        $this->view->message = t('Message: %0%', nl2br($this->httpRequest->post('message')));

        $this->view->footer_title = t('User information');
        $this->view->footer_content =
        '<p>' . t('User IP: %ip%') . '</p>
         <p>' . t('Browser info: %0%', $this->browser->getUserAgent()) . '</p>
         <p>' . t('User come from: %0%', '<a href="'.$this->httpRequest->currentUrl().'">'.t('URL Page').'</a>') . '</p>';

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'globals/' . PH7_VIEWS . PH7_TPL_NAME . '/mails/sys/mod/contact/contact_form.tpl', $sFeedbackEmail);

        $aInfo = [
          'from' => $this->httpRequest->post('mail'),
          'form_name' => $this->httpRequest->post('last_name') . ' ' . $this->httpRequest->post('first_name'),
          'subject' => t('Message from contact form %0%', $this->httpRequest->post('subject')),
          'to' => $sFeedbackEmail
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

}
