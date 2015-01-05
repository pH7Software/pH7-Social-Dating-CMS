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

class DesignFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $iApproved = (AdminCore::auth() || DbConfig::getSetting('profileBackgroundManualApproval') == 0) ? '1' : '0';

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

        $bWallpaper = (new UserCore)->setBackground($iProfileId, $sUsername, $_FILES['wallpaper']['tmp_name'], $iApproved);

        if (!$bWallpaper)
        {
            \PFBC\Form::setError('form_design', Form::wrongImgFileTypeMsg());
        }
        else
        {
            $sModerationText = t('Your Wallpaper has been received! But it will be visible once approved by our moderators. Please do not send a new Wallpaper because this is useless!');
            $sText =  t('Your Wallpaper has been updated successfully!');
            $sMsg = (DbConfig::getSetting('profileBackgroundManualApproval')) ? $sModerationText : $sText;
            \PFBC\Form::setSuccess('form_design', $sMsg);
        }
    }

}
