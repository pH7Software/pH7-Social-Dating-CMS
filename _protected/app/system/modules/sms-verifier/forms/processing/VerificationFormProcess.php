<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Form / Processing
 */

namespace PH7;

class VerificationFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oSmsApi = SmsGatewayFactory::create($this->config->values['module.setting']['default_sms_gateway']);
        $oSmsApi->send(
            $this->httpRequest->get('phone_number'),
            t('Your %site_name% verification code is: %0%', $this->getVerificationCode())
        );
    }

    /**
     * @return string
     */
    private function getVerificationCode()
    {
        $sUserHashValidation = (new UserCoreModel)->getHashValidation();

        return substr($sUserHashValidation, 0, 4);
    }
}
