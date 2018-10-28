<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verifier / Form / Processing
 */

namespace PH7;

use Twilio\Rest\Client;

class VerificationFormProcess
{
    public function __construct()
    {
        parent::__construct();

        $this->iApproved = (AdminCore::auth() || DbConfig::getSetting('avatarManualApproval') == 0) ? 1 : 0;

        if ($this->doesAdminEdit()) {
            $iProfileId = $this->httpRequest->get('profile_id');
            $sUsername = $this->httpRequest->get('username');
        } else {
            $iProfileId = $this->session->get('member_id');
            $sUsername = $this->session->get('member_username');
        }

        if ($this->isNudityFilterEligible()) {
            $this->checkNudityFilter();
        }

        $bAvatar = (new UserCore)->setAvatar($iProfileId, $sUsername, $_FILES['avatar']['tmp_name'], $this->iApproved);

        if (!$bAvatar) {
            \PFBC\Form::setError('form_avatar', Form::wrongImgFileTypeMsg());
        } else {
            $sModerationText = t('Your profile photo has been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
            $sText = t('Your profile photo has been updated successfully!');
            $sMsg = $this->iApproved === 0 ? $sModerationText : $sText;

            \PFBC\Form::setSuccess('form_avatar', $sMsg);
        }
    }

    public function sendSms()
    {
        $clickatell = new Client($_ENV['CLICKATELL_API_TOKEN']);

        $response = $clickatell->sendMessage(
            array($to),
            $message,
            array('from' => $this->senderId)
        );

        $status = 'FAIL';

        if (is_array($response)) {
            // Only sending one mesage so take the first one from the response.
            $messageResponse = array_pop($response);

            if ($messageResponse->error === false) {
                $status = 'OK';
            } else {
                $status = $messageResponse->error;
            }
        }

        $this->log($user_id, $to, $message, $status);

        return $status;
    }
    }
}
