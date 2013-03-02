<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Invite / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Security\Validate\Validate,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Mail\Mail;

class InviteFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $aTo = explode(',', $this->httpRequest->post('to'));
        foreach ($aTo as $sMail)
        {
            if ( !(new Validate)->email($sMail) )
            {
                \PFBC\Form::setError('form_invite',t('One or more email addresses are invalid!'));
            }
            else
            {
                $this->view->content = t('Hello!<br />You have received a privilege on the invitation from your friend on the new platform to meet new generation - %site_name%') . '<br />' .
                '<strong><a href="' . UriRoute::get('user','signup','step1', '?ref=invitation') . '">' . t('Get exclusive privilege to join your friend is waiting for you!') . '</a></strong><br />' .
                t('Message left by your friend:') . ' <br /><em>' . $this->httpRequest->post('message') . '</em>';
                $this->view->footer = t('You are receiving this message because "%0%" you know has entered your email address in the form of invitation of friends to our site. This is not spam!', $this->httpRequest->post('first_name'));

                $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'globals/' . PH7_VIEWS . PH7_TPL_NAME . '/mails/sys/mod/invite/invitation.tpl', $sMail);

                $aInfo = [
                    'to' => $sMail,
                    'subject' => t('Privilege on the invitation from your friend for the new generation community platform - %site_name%')
                ];

                if ( ! (new Mail)->send($aInfo, $sMessageHtml) )
                    \PFBC\Form::setError('form_invite', Form::errorSendingEmail());
                else
                    \PFBC\Form::setSuccess('form_invite', t('Cool, we\'ve sent that!'));
            }
        }
    }

}
