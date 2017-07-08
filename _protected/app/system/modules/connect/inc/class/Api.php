<?php
/**
 * @title          Api Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        1.3
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Util\Various;

abstract class Api
{
    protected $oDesign, $sUrl;

    public function __construct()
    {
        $this->oDesign = new Design;
    }

    /**
     * Display URL.
     *
     * @return string URL
     */
    public function __toString()
    {
        return $this->sUrl;
    }

    /**
     * Get and saves the Avatar in the temporary directory.
     *
     * @param string $sUrl
     * @return string The path of the Avatar
     */
    public function getAvatar($sUrl)
    {
        $sTmpDest = PH7_PATH_TMP . Various::genRnd() . '.jpg';
        @copy($sUrl, $sTmpDest);
        return $sTmpDest;
    }

    /**
     * Set an user authentication.
     *
     * @param integer $iId
     * @param UserCoreModel $oUserModel
     * @return void
     */
    public function setLogin($iId, UserCoreModel $oUserModel)
    {
        $oUserData = $oUserModel->readProfile($iId);
        $oUser = new UserCore;

        if (true === ($sErrMsg = $oUser->checkAccountStatus($oUserData)))
            $oUser->setAuth($oUserData, $oUserModel, new Framework\Session\Session, new Framework\Mvc\Model\Security);

        unset($oUser, $oUserModel);

        (true !== $sErrMsg) ? $this->oDesign->setFlashMsg($sErrMsg) : t('Hi %0%, welcome to %site_name%', '<em>' . $oUserData->firstName . '</em>');
    }

    /**
     * Check if gender value is correct.
     *
     * @param string $sGender The gender (sex).
     * @return string
     */
    protected function checkGender($sGender)
    {
        // Default 'female'
        return ($sGender != 'male' && $sGender != 'female' && $sGender != 'couple') ? 'female' : $sGender;
    }
}
