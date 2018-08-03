<?php
/**
 * @title          User Design Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Url;

class UserDesignCoreModel extends Design
{
    const GEO_PROFILE_LIMIT = 14;
    const CAROUSEL_PROFILE_LIMIT = 25;
    const PROFILE_BLOCK_LIMIT = 8;
    const PROFILE_LIMIT = 44;
    const GEO_PROFILE_AVATAR_SIZE = 150;
    const CAROUSEL_PROFILE_AVATAR_SIZE = 150;
    const PROFILE_BLOCK_AVATAR_SIZE = 150;
    const PROFILE_AVATAR_SIZE = 64;

    /** @var UserCore */
    protected $oUser;

    /** @var UserCoreModel */
    protected $oUserModel;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore;
        $this->oUserModel = new UserCoreModel;
    }

    /**
     * Get profile avatars from the geolocation.
     *
     * @param string $sCountryCode Optional The country code (e.g., GB, RU, FR, ES, ...). Default ''
     * @param string $sCity Optional. The city name. Default ''
     * @param int $iOffset Optional. Default 0
     * @param int $iLimit Optional. Default 14
     *
     * @return void HTML output.
     */
    public function geoProfiles(
        $sCountryCode = '',
        $sCity = '',
        $iOffset = UserCoreModel::OFFLINE_STATUS,
        $iLimit = self::GEO_PROFILE_LIMIT
    )
    {
        $oUserGeo = $this->oUserModel->getGeoProfiles($sCountryCode, $sCity, false, SearchCoreModel::LAST_ACTIVITY, $iOffset, $iLimit);
        if (empty($oUserGeo)) {
            return;
        }

        foreach ($oUserGeo as $oRow) {
            $sFirstName = $this->oStr->upperFirst($oRow->firstName);
            $sCity = $this->oStr->upperFirst($oRow->city);

            echo '<div class="carouselTooltip vs_marg pic thumb"><p><strong>';

            if (!UserCore::auth() && !AdminCore::auth()) {
                // Build GET parameters for tracking references
                $aHttpParams = [
                    'ref' => $this->oHttpRequest->currentController(),
                    'a' => 'carousel',
                    'u' => $oRow->username,
                    'f_n' => $sFirstName,
                    's' => $oRow->sex
                ];

                echo t('Meet %0% on %site_name%!', '<a href="' . $this->oUser->getProfileLink($oRow->username) . '">' . $sFirstName . '</a>'), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oRow->sex, $oRow->matchSex), '<br />', t('I from %0%, %1%.', t($oRow->country), $sCity), '</em></p><a rel="nofollow" href="', Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false), '"><img src="', $this->getUserAvatar($oRow->username, $oRow->sex, self::GEO_PROFILE_AVATAR_SIZE), '" alt="', t('Meet %0% on %site_name%', $oRow->username), '" /></a>';
            } else {
                echo t('Meet %0% on %site_name%!', $sFirstName), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oRow->sex, $oRow->matchSex), '<br />', t('I from %0%, %1%.', t($oRow->country), $sCity), '</em></p><a href="', $this->oUser->getProfileLink($oRow->username), '"><img src="', $this->getUserAvatar($oRow->username, $oRow->sex, self::GEO_PROFILE_AVATAR_SIZE), '" alt="', t('Meet %0% on %site_name%', $oRow->username), '" /></a>';
            }

            echo '</div>';
        }
    }

    /**
     * @param integer $iOffset
     * @param integer $iLimit
     */
    public function carouselProfiles($iOffset = UserCoreModel::OFFLINE_STATUS, $iLimit = self::CAROUSEL_PROFILE_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LATEST, $iOffset, $iLimit);

        if (empty($oUsers)) {
            return;
        }

        echo '<script>$(function(){$("#foo").carouFredSel()});</script>
        <div class="transparent p1"><div class="img_carousel"><div id="foo">';

        foreach ($oUsers as $oUser) {
            $sFirstName = $this->oStr->upperFirst($oUser->firstName);
            $sCity = $this->oStr->upperFirst($oUser->city);

            echo '<div class="carouselTooltip"><p><strong>';

            if (!UserCore::auth() && !AdminCore::auth()) {
                // Build GET parameters for tracking references
                $aHttpParams = [
                    'ref' => $this->oHttpRequest->currentController(),
                    'a' => 'carousel',
                    'u' => $oUser->username,
                    'f_n' => $sFirstName,
                    's' => $oUser->sex
                ];

                echo t('Meet %0% on %site_name%!', '<a href="' . $this->oUser->getProfileLink($oUser->username) . '">' . $sFirstName . '</a>'), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oUser->sex, $oUser->matchSex), '<br />', t('I from %0%, %1%.', t($oUser->country), $sCity), '</em></p><a rel="nofollow" href="', Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::CAROUSEL_PROFILE_AVATAR_SIZE), '" alt="', t('Meet %0% on %site_name%', $oUser->username), '" class="splash_avatar" /></a>';
            } else {
                echo t('Meet %0% on %site_name%!', $sFirstName), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oUser->sex, $oUser->matchSex), '<br />', t('I from %0%, %1%.', t($oUser->country), $sCity), '</em></p><a href="', $this->oUser->getProfileLink($oUser->username), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::CAROUSEL_PROFILE_AVATAR_SIZE), '" alt="', t('Meet %0% on %site_name%', $oUser->username), '" class="splash_avatar" /></a>';
            }

            echo '</div>';
        }

        echo '</div><div class="clearfix"></div></div></div>';
    }

    /**
     * @param integer $iOffset
     * @param integer $iLimit
     */
    public function profilesBlock($iOffset = UserCoreModel::OFFLINE_STATUS, $iLimit = self::PROFILE_BLOCK_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LATEST, $iOffset, $iLimit);
        if (empty($oUsers)) {
            return;
        }

        echo '<ul class="zoomer_pic">';

        foreach ($oUsers as $oUser) {
            $sFirstName = $this->oStr->upperFirst($oUser->firstName);

            echo '<li><a rel="nofollow" href="', $this->oUser->getProfileSignupLink($oUser->username, $sFirstName, $oUser->sex), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::PROFILE_BLOCK_AVATAR_SIZE), '" alt="', t('Meet %0% on %site_name%', $oUser->username), '" /></a></li>';
        }

        echo '</ul>';
    }

    /**
     * @param integer $iOffset
     * @param integer $iLimit
     */
    public function profiles($iOffset = UserCoreModel::OFFLINE_STATUS, $iLimit = self::PROFILE_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LAST_ACTIVITY, $iOffset, $iLimit);
        if (empty($oUsers)) {
            return;
        }

        foreach ($oUsers as $oUser) {
            (new AvatarDesignCore)->get($oUser->username, $oUser->firstName, $oUser->sex, self::PROFILE_AVATAR_SIZE);
        }
    }

    /**
     * @param integer $iProfileId
     */
    public static function userStatus($iProfileId)
    {
        $oUserModel = new UserCoreModel;

        echo '<div class="user_status">';

        if ($oUserModel->isOnline($iProfileId, DbConfig::getSetting('userTimeout'))) {
            $sCssClass = 'green';
            $sTxt = t('Online!');
        } else {
            $iStatus = $oUserModel->getUserStatus($iProfileId);
            $sCssClass = ($iStatus === UserCoreModel::BUSY_STATUS ? 'orange' : ($iStatus === UserCoreModel::AWAY_STATUS ? 'red' : 'gray'));
            $sTxt = ($iStatus === UserCoreModel::BUSY_STATUS ? t('Busy') : ($iStatus === UserCoreModel::AWAY_STATUS ? t('Away') : t('Offline')));
        }

        echo '<span class="', $sCssClass, '" title="', $sTxt, '">•</span>';

        echo '</div>';

        unset($oUserModel);
    }
}
