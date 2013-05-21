<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mail\Mail,
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Url\HeaderUrl;

class MailFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oUserModel = new UserCoreModel;
        $oMailModel = new MailModel;

        $sMessage = $this->httpRequest->post('message', HttpRequest::ONLY_XSS_CLEAN);
        $sCurrentTime = $this->dateTime->get()->dateTime('Y-m-d H:i:s');
        $iTimeDelay = (int) DbConfig::getSetting('timeDelaySendMail');
        $sRecipient = $this->httpRequest->post('recipient');
        $iRecipientId = $oUserModel->getId(null, $sRecipient);
        $iSenderId = (int) $this->session->get('member_id');

        if ($iSenderId == $iRecipientId)
        {
            \PFBC\Form::setError('form_compose_mail', t('Oops! You can not send a message to yourself.'));
        }
        elseif ($sRecipient == PH7_ADMIN_USERNAME)
        {
            \PFBC\Form::setError('form_compose_mail', t('Oops! You cannot reply to administrator! If you want to contact us, please use our <a href="%0%">contact form</a>.', UriRoute::get('contact', 'contact', 'index')));
        }
        elseif ( ! (new ExistsCoreModel)->id($iRecipientId, 'Members') )
        {
            \PFBC\Form::setError('form_compose_mail', t('Oops! The username "%0%" does not exist.', escape(substr($this->httpRequest->post('recipient'),0, PH7_MAX_USERNAME_LENGTH), true)));
        }
        elseif (!$oMailModel->checkWaitSend($iSenderId, $iTimeDelay, $sCurrentTime))
        {
            \PFBC\Form::setError('form_compose_mail', Form::waitWriteMsg($iTimeDelay));
        }
        elseif ($oMailModel->isDuplicateContent($iSenderId, $sMessage))
        {
            \PFBC\Form::setError('form_compose_mail', Form::duplicateContentMsg());
        }
        else
        {
            $iSenderId = (AdminCore::auth() && !$this->session->exists('login_user_as')) ? PH7_ADMIN_ID : $iSenderId;

            $mSendMsg = $oMailModel->sendMessage($iSenderId, $iRecipientId, $this->httpRequest->post('title'), $sMessage, $sCurrentTime);

            if (false === $mSendMsg)
            {
                \PFBC\Form::setError('form_compose_mail', t('Problem while sending the message. Please try again later.'));
            }
            else
            {
                // If the message recipient isn't connected NOW, we send a message.
                if (!$oUserModel->isOnline($iRecipientId, 0))
                {
                    $this->view->content = t('Hello %0%!<br />You\'ve a new private message of <strong>%1%</strong>.<br /> <a href="%2%">Click here</a> to read your message.', $this->httpRequest->post('recipient'), $this->session->get('member_username'), UriRoute::get('mail', 'main', 'inbox', $mSendMsg));

                    $sRecipientEmail = $oUserModel->getEmail($iRecipientId);

                    $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'globals/' . PH7_VIEWS . PH7_TPL_NAME . '/mails/sys/mod/mail/new_msg.tpl', $sRecipientEmail);

                    $aInfo = [
                        'to' => $sRecipientEmail,
                        'subject' => t('You\'ve a new Private Message - %site_name%')
                    ];

                    (new Mail)->send($aInfo, $sMessageHtml);

                    HeaderUrl::redirect(UriRoute::get('mail', 'main', 'index'), t('Your message has been sent successfully!'));
                }
            }

            unset($oUserModel, $oMailModel);
        }
    }

}
