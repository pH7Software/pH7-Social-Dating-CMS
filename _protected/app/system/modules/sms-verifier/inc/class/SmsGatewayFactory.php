<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Inc / Class
 */

namespace PH7;

use PH7\Framework\Config\Config;

class SmsGatewayFactory
{
    const EXCEPTION_ERROR_MESSAGE = '"%s" is an invalid SMS gateway specified.';

    const GATEWAYS = [
        'clickatell' => ClickatellProvider::class,
        'twilio' => TwilioProvider::class
    ];

    /**
     * @param string $sSmsGateway
     *
     * @return SmsProvidable
     *
     * @throws InvalidSmsGatewayException
     */
    public static function create($sSmsGateway)
    {
        if (!self::isGatewayValid($sSmsGateway)) {
            throw new InvalidSmsGatewayException(
                sprintf(self::EXCEPTION_ERROR_MESSAGE, $sSmsGateway)
            );
        }

        $sApiToken = Config::getInstance()->values['module.setting']['clickatell.api_token'];

        $sClassName = self::GATEWAYS[$sSmsGateway];
        return new $sClassName($sApiToken);
    }

    /**
     * @param string $sSmsGateway
     *
     * @return bool
     */
    private static function isGatewayValid($sSmsGateway)
    {
        return in_array($sSmsGateway, self::GATEWAYS, true);
    }
}
