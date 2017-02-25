<?php
/**
 * @title          User Design Core Model Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model / Design
 */
namespace PH7;

use
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Url;

class UserDesignCoreModel extends Framework\Mvc\Model\Design
{
    const
    GEO_PROFILE_LIMIT = 14,
    CAROUSEL_PROFILE_LIMIT = 25,
    PROFILE_BLOCK_LIMIT = 8,
    PROFILE_LIMIT = 44,
    GEO_PROFILE_AVATAR_SIZE = 150,
    CAROUSEL_PROFILE_AVATAR_SIZE = 150,
    PROFILE_BLOCK_AVATAR_SIZE = 150,
    PROFILE_AVATAR_SIZE = 64;

    protected $oUser, $oUserModel;

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
     * @param integer $iOffset Optional. Default 0
     * @param integer $iLimit Optional. Default 14
     * @return void HTML output.
     */
    public function geoProfiles($sCountryCode = '', $sCity = '', $iOffset = 0, $iLimit = self::GEO_PROFILE_LIMIT)
    {
        $oUserGeo = $this->oUserModel->getGeoProfiles($sCountryCode, $sCity, false, SearchCoreModel::LAST_ACTIVITY, $iOffset, $iLimit);
        if (empty($oUserGeo)) return;

        foreach ($oUserGeo as $oRow)
        {
            $sFirstName = $this->oStr->upperFirst($oRow->firstName);
            $sCity = $this->oStr->upperFirst($oRow->city);

            echo '<div class="carouselTooltip vs_marg pic thumb"><p><strong>';

            if (!UserCore::auth() && !AdminCore::auth())
            {
                // Build GET parameters for tracking references
                $aHttpParams = [
                    'ref' => $this->oHttpRequest->currentController(),
                    'a' => 'carousel',
                    'u' => $oRow->username,
                    'f_n' => $sFirstName,
                    's' => $oRow->sex
                ];

                echo t('Meet %0% on %site_name%!', '<a href="' . $this->oUser->getProfileLink($oRow->username) . '">'. $sFirstName . '</a>'), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oRow->sex, $oRow->matchSex), '<br />', t('I from %0%, %1%.', t($oRow->country), $sCity), '</em></p><a rel="nofollow" href="', Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false), '"><img src="', $this->getUserAvatar($oRow->username, $oRow->sex, self::GEO_PROFILE_AVATAR_SIZE, 'Members'), '" alt="', t('Meet %0% on %site_name%', $oRow->username), '" /></a>';
            }
            else
            {
                echo t('Meet %0% on %site_name%!', $sFirstName), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oRow->sex, $oRow->matchSex), '<br />', t('I from %0%, %1%.', t($oRow->country), $sCity), '</em></p><a href="', $this->oUser->getProfileLink($oRow->username), '"><img src="', $this->getUserAvatar($oRow->username, $oRow->sex, self::GEO_PROFILE_AVATAR_SIZE, 'Members'), '" alt="', t('Meet %0% on %site_name%', $oRow->username), '" /></a>';
            }

            echo '</div>';
        }
    }

    public function carouselProfiles($iOffset = 0, $iLimit = self::CAROUSEL_PROFILE_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LATEST, $iOffset, $iLimit);
        if (empty($oUsers)) return;

        echo '<script>$(function(){$("#foo").carouFredSel()});</script>
        <div class="transparent p1"><div class="img_carousel"><div id="foo">';

        foreach ($oUsers as $oUser)
        {
            $sFirstName = $this->oStr->upperFirst($oUser->firstName);
            $sCity = $this->oStr->upperFirst($oUser->city);

            echo '<div class="carouselTooltip"><p><strong>';

            if (!UserCore::auth() && !AdminCore::auth())
            {
                // Build GET parameters for tracking references
                $aHttpParams = [
                    'ref' => $this->oHttpRequest->currentController(),
                    'a' => 'carousel',
                    'u' => $oUser->username,
                    'f_n' => $sFirstName,
                    's' => $oUser->sex
                ];

                echo t('Meet %0% on %site_name%!', '<a href="' . $this->oUser->getProfileLink($oUser->username) . '">' . $sFirstName . '</a>'), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oUser->sex, $oUser->matchSex), '<br />', t('I from %0%, %1%.', t($oUser->country), $sCity), '</em></p><a rel="nofollow" href="', Uri::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery($aHttpParams), false), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::CAROUSEL_PROFILE_AVATAR_SIZE, 'Members'), '" alt="',t('Meet %0% on %site_name%', $oUser->username), '" class="splash_avatar" /></a>';
            }
            else
            {
                echo t('Meet %0% on %site_name%!', $sFirstName), '</strong><br /><em>', t('I am a %0% and I am looking %1%.', $oUser->sex, $oUser->matchSex), '<br />', t('I from %0%, %1%.', t($oUser->country), $sCity), '</em></p><a href="', $this->oUser->getProfileLink($oUser->username), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::CAROUSEL_PROFILE_AVATAR_SIZE, 'Members'), '" alt="',t('Meet %0% on %site_name%', $oUser->username), '" class="splash_avatar" /></a>';
            }

            echo '</div>';
        }

        echo '</div><div class="clearfix"></div></div></div>';
    }

    public function profilesBlock($iOffset = 0, $iLimit = self::PROFILE_BLOCK_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LATEST, $iOffset, $iLimit);
        if (empty($oUsers)) return;

        echo '<ul class="zoomer_pic">';

        foreach ($oUsers as $oUser)
        {
            $sFirstName = $this->oStr->upperFirst($oUser->firstName);
            $sCity = $this->oStr->upperFirst($oUser->city);

            echo '<li><a rel="nofollow" href="', $this->oUser->getProfileSignupLink($oUser->username, $sFirstName, $oUser->sex), '"><img src="', $this->getUserAvatar($oUser->username, $oUser->sex, self::PROFILE_BLOCK_AVATAR_SIZE, 'Members'), '" alt="',t('Meet %0% on %site_name%', $oUser->username), '" /></a></li>';
        }

        echo '</ul>';
    }

    public function profiles($iOffset = 0, $iLimit = self::PROFILE_LIMIT)
    {
        $oUsers = $this->oUserModel->getProfiles(SearchCoreModel::LAST_ACTIVITY, $iOffset, $iLimit);
        if (empty($oUsers)) return;

        foreach ($oUsers as $oUser)
            (new AvatarDesignCore)->get($oUser->username, $oUser->firstName, $oUser->sex, self::PROFILE_AVATAR_SIZE);
    }

    public static function userStatus($iProfileId)
    {
        $oUserModel = new \PH7\UserCoreModel;

        echo '<div class="user_status">';
        if ($oUserModel->isOnline($iProfileId, Framework\Mvc\Model\DbConfig::getSetting('userTimeout')))
        {
            echo '<img src="', PH7_URL_TPL, PH7_TPL_NAME, PH7_SH, PH7_IMG, 'icon/online.png" alt="', t('Online'), '" title="', t('Is Online!'), '" />';
        }
        else
        {
            $iStatus = $oUserModel->getUserStatus($iProfileId);
            $sImgName = ($iStatus == 2 ? 'busy' : ($iStatus == 3 ? 'away' : 'offline'));
            $sTxt = ($iStatus == 2 ? t('Busy') : ($iStatus == 3 ? t('Away') : t('Offline')));

            echo '<img src="', PH7_URL_TPL, PH7_TPL_NAME, PH7_SH, PH7_IMG, 'icon/', $sImgName, '.png" alt="', $sTxt, '" title="', $sTxt, '" />';
        }
        echo '</div>';

        unset($oUserModel);
    }
}
