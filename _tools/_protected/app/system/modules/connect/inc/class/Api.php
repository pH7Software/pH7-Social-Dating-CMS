<?php
/**
 * @title          Api Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
    const BIRTH_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_GENDER = GenderTypeUserCore::FEMALE;
    const DEFAULT_AVATAR_EXTENSION = '.jpg';

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
        $sTmpDest = PH7_PATH_TMP . Various::genRnd() . self::DEFAULT_AVATAR_EXTENSION;
        @copy($sUrl, $sTmpDest);
        return $sTmpDest;
    }

    /**
     * Set an user authentication.
     *
     * @param int $iId
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
     * @return string
     */
    protected function getDefaultUserBirthDate()
    {
        return date(self::BIRTH_DATE_FORMAT, strtotime('-30 year'));
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
        return !GenderTypeUserCore::isGenderValid($sGender) ? self::DEFAULT_GENDER : $sGender;
    }
}
