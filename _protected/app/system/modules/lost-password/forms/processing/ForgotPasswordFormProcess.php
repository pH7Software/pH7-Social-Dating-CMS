<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Util\Various;
use stdClass;

class ForgotPasswordFormProcess extends Form
{
    const BRUTE_FORCE_SLEEP_DELAY = 1;

    /** @var UserCoreModel */
    private $oUserModel;

    /**
     * @param string $sTable
     */
    public function __construct($sTable)
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $sEmail = $this->httpRequest->post('mail');

        if (!$iProfileId = $this->oUserModel->getId($sEmail, null, $sTable)) {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);
            \PFBC\Form::setError(
                'form_forgot_password',
                t('Oops, this "%0%" is not associated with any %site_name% account. Please, make sure that you entered the e-mail address used in creating your account.', escape(substr($sEmail, 0, PH7_MAX_EMAIL_LENGTH)))
            );
        } else {
            $this->oUserModel->setNewHashValidation($iProfileId, Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH), $sTable);
            (new UserCore)->clearReadProfileCache($iProfileId, $sTable); // Clean the profile data (for the new hash)

            if (!$this->sendMail($sTable, $iProfileId)) {
                \PFBC\Form::setError('form_forgot_password', Form::errorSendingEmail());
            } else {
                \PFBC\Form::setSuccess('form_forgot_password', t('Password reset instructions sent to %0%', $sEmail));
            }
        }
    }

    /**
     * @param string $sTable DB table name.
     * @param int $iProfileId The user profile ID.
     *
     * @return int Number of recipients who were accepted for delivery.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendMail($sTable, $iProfileId)
    {
        $oData = $this->oUserModel->readProfile($iProfileId, $sTable);
        $sResetPwdUrl = $this->getResetPasswordUrl($oData);

        $this->view->content = t('Hello %0%!', $oData->username) . '<br />' .
            t('Someone (from the IP: %0%) has requested a new password for this account.', $this->design->ip(null, false)) . '<br />' .
            t('If you requested it, click on the link below, otherwise please ignore this email and your password will remain unchanged.') .
            '<br /><a href="' . $sResetPwdUrl . '">' . $sResetPwdUrl . '</a>';

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/lost-password/confirm-lost-password.tpl', $oData->email);

        $aInfo = [
            'to' => $oData->email,
            'subject' => t('Request for new password - %site_name%')
        ];

        unset($oData);

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    private function getResetPasswordUrl(stdClass $oData)
    {
        /**
         * @internal We place the email and hash outside of `Uri::get()`,
         * otherwise special characters (such as `@`) will be renamed and the parameters passed in the URL will be unusable thereafter.
         * */
        $sResetPwdUrl = Uri::get(
            'lost-password',
            'main',
            'reset',
            $this->httpRequest->get('mod')
        );
        $sResetPwdUrl .= PH7_SH . $oData->email . PH7_SH . $oData->hashValidation;

        return $sResetPwdUrl;
    }
}
