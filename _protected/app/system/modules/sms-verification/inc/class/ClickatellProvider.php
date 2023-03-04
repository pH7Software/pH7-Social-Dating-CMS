<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use Clickatell\ClickatellException;
use Clickatell\Rest as Client;
use PH7\Framework\Error\Logger;

class ClickatellProvider extends SmsProvider implements SmsProvidable
{
    public function send(string $sPhoneNumber, string $sTextMessage): bool
    {
        $oClickatell = new Client($this->sApiToken);

        try {
            $aResponse = $oClickatell->sendMessage(
                [
                    'to' => [$sPhoneNumber],
                    'content' => $sTextMessage
                ],
                [
                    'from' => $this->sSenderNumber
                ]
            );

            if (!empty($aResponse) && is_array($aResponse)) {
                $aMessages = $aResponse['messages'];
                $aMessage = array_pop($aMessages);

                if ($aMessage['error'] === false) {
                    return true;
                }
            }

            return false;
        } catch (ClickatellException $oExcept) {
            (new Logger())->msg('Clickatell error while sending SMS: ' . $oExcept->getMessage());
            return false;
        }
    }
}
