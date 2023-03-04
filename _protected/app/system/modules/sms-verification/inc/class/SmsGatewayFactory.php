<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;

class SmsGatewayFactory
{
    private const CLICKATELL_NAME = 'clickatell';
    private const TWILIO_NAME = 'twilio';
    private const INVALID_SMS_GATEWAY_MESSAGE = '"%s" is an invalid SMS gateway specified.';

    /**
     * @throws InvalidSmsGatewayException
     */
    public static function create(string $sSmsGateway): SmsProvidable
    {
        switch ($sSmsGateway) {
            case self::CLICKATELL_NAME:
                $sSenderNumber = Config::getInstance()->values['module.setting']['clickatell.sender.phone_number'];
                $sApiToken = Config::getInstance()->values['module.setting']['clickatell.api_token'];
                return new ClickatellProvider($sSenderNumber, $sApiToken);

            case self::TWILIO_NAME:
                $sSenderNumber = Config::getInstance()->values['module.setting']['twilio.sender.phone_number'];
                $sApiToken = Config::getInstance()->values['module.setting']['twilio.api_token'];
                $sApiId = Config::getInstance()->values['module.setting']['twilio.api_id'];
                return new TwilioProvider($sSenderNumber, $sApiToken, $sApiId);

            default:
                throw new InvalidSmsGatewayException(
                    sprintf(self::INVALID_SMS_GATEWAY_MESSAGE, $sSmsGateway)
                );
        }
    }
}
