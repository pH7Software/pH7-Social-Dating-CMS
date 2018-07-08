<?php
/**
 * @title          Api Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Inc / Class
 * @version        1.3
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly as CurlySyntax;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;

abstract class Api
{
    /** @var Design */
    protected $oDesign;

    /** @var PH7Tpl */
    protected $oView;

    /** @var string */
    protected $sUrl;

    public function __construct()
    {
        $this->oDesign = new Design;
        $this->oView = new PH7Tpl(new CurlySyntax);
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
     *
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
     *
     * @return void
     */
    public function setLogin($iId, UserCoreModel $oUserModel)
    {
        $oUserData = $oUserModel->readProfile($iId);
        $oUser = new UserCore;

        if (true === ($sErrMsg = $oUser->checkAccountStatus($oUserData))) {
            $oUser->setAuth($oUserData, $oUserModel, new Session, new SecurityModel);
        }
        unset($oUser);

        (true !== $sErrMsg) ? $this->oDesign->setFlashMsg($sErrMsg) : t('Hi %0%, welcome to %site_name%', '<em>' . $oUserData->firstName . '</em>');
    }

    /**
     * Check if gender value is correct.
     *
     * @param string $sGender The gender (sex).
     *
     * @return string
     */
    protected function checkGender($sGender)
    {
        // Default 'female'
        return ($sGender != 'male' && $sGender != 'female' && $sGender != 'couple') ? 'female' : $sGender;
    }
}
