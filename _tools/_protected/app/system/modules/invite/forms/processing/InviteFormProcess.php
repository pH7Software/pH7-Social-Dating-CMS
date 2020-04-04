<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Invite / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Validate\Validate;

class InviteFormProcess extends Form
{
    const MAX_EMAIL_ADDRESSES = 10;
    const EMAIL_DELIMITER = ',';

    public function __construct()
    {
        parent::__construct();

        $aTo = $this->getEmails();
        if (count($aTo) > self::MAX_EMAIL_ADDRESSES) {
            \PFBC\Form::setError(
                'form_invite',
                t('To prevent spam, you cannot put more than %0% email addresses at a time.', self::MAX_EMAIL_ADDRESSES)
            );
        } else {
            $oMail = new Mail;
            foreach ($aTo as $sEmailAddress) {
                if (!(new Validate)->email($sEmailAddress)) {
                    \PFBC\Form::setError('form_invite', t('One or more email addresses are invalid!'));
                } else {
                    if (!$this->sendMail($sEmailAddress, $oMail)) {
                        \PFBC\Form::setError('form_invite', Form::errorSendingEmail());
                    } else {
                        \PFBC\Form::setSuccess('form_invite', t('Cool! We have sent that.'));
                    }
                }
            }
            unset($oMail);
        }
    }

    /**
     * Send the confirm email.
     *
     * @param string $sEmailAddress The user email.
     * @param Mailable $oMailEngine
     *
     * @return int Number of recipients who were accepted for delivery.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function sendMail($sEmailAddress, Mailable $oMailEngine)
    {
        $this->view->content = t('Hello!') . '<br />' .
            t('You have received a privilege on the invitation from your friend on the new platform to meet new generation - %site_name%') . '<br />' .
            '<strong><a href="' . Uri::get('user', 'signup', 'step1', '?ref=invitation') . '">' . t('Get exclusive privilege to join your friend is waiting for you!') . '</a></strong><br />' .
            t('Message left by your friend:') . '<br />"<em>' . $this->httpRequest->post('message') . '</em>"';
        $this->view->footer = t('You are receiving this message because "%0%" you know has entered your email address in the form of invitation of friends to our site. This is not spam!', $this->httpRequest->post('first_name'));

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/invite/invitation.tpl',
            $sEmailAddress
        );

        $aInfo = [
            'to' => $sEmailAddress,
            'subject' => t('Privilege on the invitation from your friend for the new generation community platform - %site_name%')
        ];

        return $oMailEngine->send($aInfo, $sMessageHtml);
    }

    /**
     * @return array
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function getEmails()
    {
        return explode(
            self::EMAIL_DELIMITER,
            $this->httpRequest->post('to')
        );
    }
}
