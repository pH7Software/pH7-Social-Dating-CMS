<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use PH7\Framework\Error\Logger;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class TwilioProvider extends SmsProvider implements SmsProvidable
{
    public function send(string $sPhoneNumber, string $sTextMessage): bool
    {
        $oClient = new Client($this->sApiId, $this->sApiToken);

        try {
            $oMessage = $oClient->messages->create(
                $sPhoneNumber,
                [
                    'from' => $this->sSenderNumber,
                    'body' => $sTextMessage
                ]
            );

            return strlen($oMessage->sid) > 1;
        } catch (TwilioException $oExcept) {
            (new Logger())->msg('Twilio error while sending SMS: ' . $oExcept->getMessage());

            return false;
        }
    }
}
