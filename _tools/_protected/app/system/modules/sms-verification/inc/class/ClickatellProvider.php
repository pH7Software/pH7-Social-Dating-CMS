<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

namespace PH7;

use Clickatell\ClickatellException;
use Clickatell\Rest as Client;

class ClickatellProvider extends SmsProvider implements SmsProvidable
{
    /**
     * {@inheritdoc}
     */
    public function send($sPhoneNumber, $sTextMessage)
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
            return false;
        }
    }
}
