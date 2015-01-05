<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use
PH7\Framework\Ip\Ip,
PH7\Framework\Util\Various,
PH7\Framework\Mail\Mail,
PH7\Framework\Mvc\Router\Uri;

class ForgotPasswordFormProcess extends Form
{

    public function __construct($sTable)
    {
        parent::__construct();

        $oUserModel = new UserCoreModel;
        $sMail = $this->httpRequest->post('mail');

        if (!$iProfileId = $oUserModel->getId($sMail, null, $sTable))
        {
            sleep(1); // Security against brute-force attack to avoid drowning the server and the database
            \PFBC\Form::setError('form_forgot_password', t('Oops, this "%0%" is not associated with any %site_name% account. Please, make sure that you entered the e-mail address used in creating your account.', escape(substr($sMail,0,PH7_MAX_EMAIL_LENGTH))));
        }
        else
        {
            $oUserModel->setNewHashValidation($iProfileId, Various::genRnd(), $sTable);
            (new UserCore)->clearReadProfileCache($iProfileId, $sTable); // Clean the profile data (for the new hash)
            $oData = $oUserModel->readProfile($iProfileId, $sTable);

            /** We place the text outside of Uri::get() otherwise special characters will be deleted and the parameters passed in the url will be unusable thereafter. **/
            $sResetUrl = Uri::get('lost-password', 'main', 'reset', $this->httpRequest->get('mod')) . PH7_SH . $oData->email . PH7_SH . $oData->hashValidation;

            $this->view->content = t('Hello %0%!<br />Somebody (from the IP address %1%) has requested a new password for their account.', $oData->username, Ip::get()) . '<br />' .
            t('If you requested for this, click on the link below, otherwise ignore this email and your password will remain unchanged.') .
            '<br /><a href="' . $sResetUrl . '">' . $sResetUrl . '</a>';

            $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_NAME . '/mail/sys/mod/lost-password/confirm-lost-password.tpl', $oData->email);

            $aInfo = [
                'to' => $oData->email,
                'subject' => t('Request for new password - %site_name%')
            ];

            unset($oData);

            if ( ! (new Mail)->send($aInfo, $sMessageHtml) )
                \PFBC\Form::setError('form_forgot_password', Form::errorSendingEmail());
            else
                \PFBC\Form::setSuccess('form_forgot_password', t('Successfully requested a new password, email sent!'));
        }
        unset($oUserModel);
    }

}
