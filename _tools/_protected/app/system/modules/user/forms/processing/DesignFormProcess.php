<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Moderation\Filter;

class DesignFormProcess extends Form implements NudityDetectable
{
    /** @var int */
    private $iApproved;

    public function __construct()
    {
        parent::__construct();

        $this->iApproved = (AdminCore::auth() || DbConfig::getSetting('bgProfileManualApproval') == 0) ? 1 : 0;

        if (AdminCore::auth() && !User::auth() && $this->httpRequest->getExists(['profile_id', 'username'])) {
            $iProfileId = $this->httpRequest->get('profile_id');
            $sUsername = $this->httpRequest->get('username');
        } else {
            $iProfileId = $this->session->get('member_id');
            $sUsername = $this->session->get('member_username');
        }

        if ($this->isNudityFilterEligible()) {
            $this->checkNudityFilter();
        }

        $bWallpaper = (new UserCore)->setBackground(
            $iProfileId,
            $sUsername,
            $_FILES['wallpaper']['tmp_name'],
            $this->iApproved
        );

        if (!$bWallpaper) {
            \PFBC\Form::setError('form_design', Form::wrongImgFileTypeMsg());
        } else {
            $sModerationText = t('Your Wallpaper has been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
            $sText = t('Your Wallpaper has been updated successfully!');
            $sMsg = $this->iApproved === 0 ? $sModerationText : $sText;
            \PFBC\Form::setSuccess('form_design', $sMsg);
        }
    }

    public function isNudityFilterEligible()
    {
        return $this->iApproved === 1 && !AdminCore::auth() && DbConfig::getSetting('nudityFilter');
    }

    public function checkNudityFilter()
    {
        if (Filter::isNudity($_FILES['wallpaper']['tmp_name'])) {
            // The wallpaper doesn't seem suitable for everyone. Overwrite "$iApproved" and set the image for approval
            $this->iApproved = 0;
        }
    }
}
