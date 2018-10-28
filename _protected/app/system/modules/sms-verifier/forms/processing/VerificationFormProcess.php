<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Form / Processing
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class VerificationFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        if ($this->isVerificationCodeValid()) {
            Header::redirect(
                Uri::get('user', 'main', 'login'),
                t('Congratulations! Your phone number has been successfully verified.')
            );
        } else {
            \PFBC\Form::setError('form_sms_verification', t('The SMS verification code is invalid. <a href="%0%">Try to resend a new one?</a>', Uri::get('sms-verifier', 'main', 'send')));
        }
    }

    /**
     * @return bool
     */
    private function isVerificationCodeValid()
    {
        return $this->httpRequest->get('verification_code') === Verification::getVerificationCode();
    }
}
