<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Form / Processing
 */

namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Util\Various;

class ForgotPasswordFormProcess extends Form
{

    private $oUserModel;

    public function __construct($sTable)
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $sEmail = $this->httpRequest->post('mail');

        if (!$iProfileId = $this->oUserModel->getId($sEmail, null, $sTable)) {
            sleep(1); // Security against brute-force attack to avoid drowning the server and the database
            \PFBC\Form::setError('form_forgot_password', t('Oops, this "%0%" is not associated with any %site_name% account. Please, make sure that you entered the e-mail address used in creating your account.', escape(substr($sEmail, 0, PH7_MAX_EMAIL_LENGTH))));
        } else {
            $this->oUserModel->setNewHashValidation($iProfileId, Various::genRnd(), $sTable);
            (new UserCore)->clearReadProfileCache($iProfileId, $sTable); // Clean the profile data (for the new hash)


            if (!$this->sendMail($sTable, $iProfileId))
                \PFBC\Form::setError('form_forgot_password', Form::errorSendingEmail());
            else
                \PFBC\Form::setSuccess('form_forgot_password', t('Password reset instructions sent to %0%', $sEmail));
        }
    }

    /**
     * @param string $sTable DB table name.
     * @param integer $iProfileId The user profile ID.
     * @return integer Number of recipients who were accepted for delivery.
     */
    protected function sendMail($sTable, $iProfileId)
    {
        $oData = $this->oUserModel->readProfile($iProfileId, $sTable);

        /** We place the text outside of Uri::get(), otherwise special characters will be deleted and the parameters passed in the url will be unusable thereafter. **/
        $sResetUrl = Uri::get('lost-password', 'main', 'reset', $this->httpRequest->get('mod')) . PH7_SH . $oData->email . PH7_SH . $oData->hashValidation;

        $this->view->content = t('Hello %0%!', $oData->username) . '<br />' .
            t('Someone (from IP address %0%) has requested a new password for this account.', Ip::get()) . '<br />' .
            t('If you requested it, click on the link below, otherwise please ignore this email and your password will remain unchanged.') .
            '<br /><a href="' . $sResetUrl . '">' . $sResetUrl . '</a>';

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/lost-password/confirm-lost-password.tpl', $oData->email);

        $aInfo = [
            'to' => $oData->email,
            'subject' => t('Request for new password - %site_name%')
        ];

        unset($oData);

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

}
