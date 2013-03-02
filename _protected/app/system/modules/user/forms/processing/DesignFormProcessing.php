<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class DesignFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $iApproved = (DbConfig::getSetting('profileBackgroundManualApproval') == 0) ? '1' : '0';
        $bWallpaper = (new UserCore)->setBackground($this->session->get('member_id'), $this->session->get('member_username'), $_FILES['wallpaper']['tmp_name'], $iApproved);

        if(!$bWallpaper) {
            \PFBC\Form::setError('form_design', Form::wrongImgFileTypeMsg());
        } else {
            $sModerationText = t('Your Wallpaper has been received! But it will be visible once approved by our moderators. Please do not send a new Wallpaper because this is useless!');
            $sText =  t('Your Wallpaper has been updated successfully!');
            $sMsg = (DbConfig::getSetting('profileBackgroundManualApproval')) ? $sModerationText : $sText;
            \PFBC\Form::setSuccess('form_design', $sMsg);
        }
    }

}
