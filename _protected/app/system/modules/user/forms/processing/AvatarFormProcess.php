<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class AvatarFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $iApproved = (AdminCore::auth() || DbConfig::getSetting('avatarManualApproval') == 0) ? '1' : '0';

        if (AdminCore::auth() && !User::auth() && $this->httpRequest->getExists( array('profile_id', 'username') ))
        {
            $iProfileId = $this->httpRequest->get('profile_id');
            $sUsername = $this->httpRequest->get('username');
        }
        else
        {
            $iProfileId = $this->session->get('member_id');
            $sUsername = $this->session->get('member_username');
        }

        $bAvatar = (new UserCore)->setAvatar($iProfileId, $sUsername, $_FILES['avatar']['tmp_name'], $iApproved);

        if (!$bAvatar)
        {
            \PFBC\Form::setError('form_avatar', Form::wrongImgFileTypeMsg());
        }
        else
        {
            $sModerationText = t('Your avatar has been received! But it will be visible once approved by our moderators. Please do not send a new avatar because this is useless!');
            $sText =  t('Your avatar has been updated successfully!');
            $sMsg = ($iApproved == '0') ? $sModerationText : $sText;
            \PFBC\Form::setSuccess('form_avatar', $sMsg);
        }
    }

}
