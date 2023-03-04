<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Url;
use stdClass;

abstract class ProfileBaseController extends Controller
{
    use ImageTaggable;

    const SOCIAL_TAG_AVATAR_SIZE = 400;

    /**
     * Default Map settings.
     * These constants are likely to be modified in the child class
     * thanks to static:: keyword to use late static binding.
     */
    const MAP_ZOOM_LEVEL = 10;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '260px';

    protected UserCoreModel $oUserModel;

    protected int $iProfileId;

    protected int $iVisitorId;

    protected bool $bUserAuth;

    /**
     * Index displaying the user profile page.
     */
    abstract public function index(): void;

    /**
     * Add CSS files for the profile page's design.
     */
    abstract protected function addCssFiles(): void;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $this->bUserAuth = UserCore::auth();

        // Initialize header tpl variable, to make sure it won't be overwritten later on
        $this->view->header = '';
    }

    /**
     * @return bool TRUE if the user is on their own profile, FALSE otherwise.
     */
    public function isOwnProfile(): bool
    {
        return $this->str->equals($this->iVisitorId, $this->iProfileId);
    }

    /**
     * @param int $iProfileId
     */
    protected function setProfileId($iProfileId): void
    {
        $this->iProfileId = (int)$iProfileId;
    }

    /**
     * @param int $iVisitorId
     */
    protected function setVisitorId($iVisitorId): void
    {
        $this->iVisitorId = (int)$iVisitorId;
    }

    public function getProfileId(): int
    {
        return $this->iProfileId;
    }

    public function getVisitorId(): int
    {
        return $this->iVisitorId;
    }

    /**
     * Privacy Profile.
     *
     * @throws Framework\File\IOException
     */
    protected function initPrivacy(stdClass $oUser): void
    {
        $oUserPrivacyViews = $this->oUserModel->getPrivacySetting($this->iProfileId);

        if ($oUserPrivacyViews->searchProfile === PrivacyCore::NO) {
            $this->excludeProfileFromSearchEngines();
        }

        if (!$this->bUserAuth && $oUserPrivacyViews->privacyProfile === PrivacyCore::ONLY_USERS) {
            $this->view->error = t(
                'Whoops! "%0%" profile is only visible to members. Please <a href="%1%">login</a> or <a href="%2%">register</a> to see this profile.',
                $oUser->username,
                Uri::get('user', 'main', 'login'),
                Uri::get('user', 'signup', 'step1')
            );
        } elseif ($oUserPrivacyViews->privacyProfile === PrivacyCore::ONLY_ME && !$this->isOwnProfile()) {
            $this->view->error = t('Whoops! "%0%" profile is not available to you.', $oUser->username);
        }

        if ($this->bUserAuth) {
            $this->updateProfileViews($oUserPrivacyViews);
        }
        unset($oUserPrivacyViews);
    }

    protected function setMenuBar(string $sFirstName, stdClass $oUser): void
    {
        if (SysMod::isEnabled('mail')) {
            $this->view->mail_link = $this->getMailLink($sFirstName, $oUser);
        }

        if (SysMod::isEnabled('im')) {
            $this->view->messenger_link = $this->getMessengerLink($sFirstName, $oUser);
        }

        if (SysMod::isEnabled('friend')) {
            $this->view->friend_link = $this->getFriendLink($sFirstName, $oUser);
            $this->view->is_approved_friend = $this->isFriend(FriendCoreModel::APPROVED_REQUEST);
            $this->view->is_pending_friend = $this->isFriend(FriendCoreModel::PENDING_REQUEST);
        }
    }

    /**
     * Set the Google Maps code to the view.
     */
    protected function setMap(string $sCity, string $sCountry, stdClass $oUser): void
    {
        $sFullAddress = $sCity . ' ' . t($sCountry);
        $sMarkerText = t('Meet <b>%0%</b> near here!', $oUser->username);

        try {
            $oMapDrawer = new MapDrawerCore(
                new Map,
                DbConfig::getSetting('googleApiKey')
            );
            $oMapDrawer->setWidthSize(self::MAP_WIDTH_SIZE);
            $oMapDrawer->setHeightSize(self::MAP_HEIGHT_SIZE);
            $oMapDrawer->setZoomLevel(self::MAP_ZOOM_LEVEL);
            $oMapDrawer->setDivId('profile_map');
            $sContent = $oMapDrawer->getMap($sFullAddress, $sMarkerText);
        } catch (PH7InvalidArgumentException $oE) {
            $sContent = sprintf('<strong>%s</strong>', $oE->getMessage());
        }

        $this->view->map = $sContent;
    }

    /**
     * @param string $sFirstName User's first name.
     * @param stdClass $oUser User data from the DB.
     *
     * @return string The anchor for the link.
     */
    protected function getMailLink(string $sFirstName, stdClass $oUser): string
    {
        if ($this->bUserAuth) {
            $sMailLink = Uri::get(
                'mail',
                'main',
                'compose',
                $oUser->username
            );
        } else {
            $aUrlParms = [
                'msg' => t('Register now to message %0%!', $sFirstName),
                'ref' => 'profile',
                'a' => 'mail',
                'u' => $oUser->username,
                'f_n' => $sFirstName,
                's' => $oUser->sex
            ];
            $sMailLink = Uri::get(
                'user',
                'signup',
                'step1',
                '?' . Url::httpBuildQuery($aUrlParms),
                false
            );
        }

        return $sMailLink;
    }

    /**
     * @param string $sFirstName User's first name.
     * @param stdClass $oUser User data from the DB.
     *
     * @return string The anchor for the link.
     */
    protected function getMessengerLink($sFirstName, stdClass $oUser): string
    {
        if ($this->bUserAuth) {
            $sMessengerLink = 'javascript:void(0)" onclick="Messenger.chatWith(\'' . $oUser->username . '\')';
        } else {
            $aUrlParms = [
                'msg' => t('Register now to talk to %0%.', $sFirstName),
                'ref' => 'profile',
                'a' => 'messenger',
                'u' => $oUser->username,
                'f_n' => $sFirstName,
                's' => $oUser->sex
            ];
            $sMessengerLink = Uri::get(
                'user',
                'signup',
                'step1',
                '?' . Url::httpBuildQuery($aUrlParms),
                false
            );
        }

        return $sMessengerLink;
    }

    /**
     * @param string $sFirstName User's first name.
     * @param stdClass $oUser User data from the DB.
     *
     * @return string The correct anchor "Manage Friend" link.
     */
    protected function getFriendLink(string $sFirstName, stdClass $oUser): string
    {
        $sCsrfToken = (new Token)->generate('friend');

        if ($this->bUserAuth) {
            if ($this->isFriend(FriendCoreModel::PENDING_REQUEST)) {
                $sFriendLink = Uri::get(
                    'friend',
                    'main',
                    'index',
                    $this->session->get('member_username') . '?looking=' . $oUser->username
                );
            } elseif ($this->isFriend(FriendCoreModel::APPROVED_REQUEST)) {
                $sFriendLink = 'javascript:void(0)" onclick="friend(\'delete\',' . $this->iProfileId . ',\'' . $sCsrfToken . '\')';
            } else {
                $sFriendLink = 'javascript:void(0)" onclick="friend(\'add\',' . $this->iProfileId . ',\'' . $sCsrfToken . '\')';
            }
        } else {
            $aUrlParms = [
                'msg' => t('Free Sign up for %site_name% to become friend with %0%.', $sFirstName),
                'ref' => 'profile',
                'a' => 'befriend',
                'u' => $oUser->username,
                'f_n' => $sFirstName,
                's' => $oUser->sex
            ];
            $sFriendLink = Uri::get(
                'user',
                'signup',
                'step1',
                '?' . Url::httpBuildQuery($aUrlParms),
                false
            );
        }

        return $sFriendLink;
    }

    /**
     * Returns filtered user/field data.
     */
    protected function getFilteredData(stdClass $oUser, stdClass $oFields): array
    {
        $sFirstName = !empty($oUser->firstName) ? $this->str->escape(
            $this->str->upperFirst($oUser->firstName),
            true
        ) : '';
        $sLastName = !empty($oUser->lastName) ? $this->str->escape($this->str->upperFirst($oUser->lastName), true) : '';
        $sMiddleName = !empty($oFields->middleName) ? $this->str->escape(
            $this->str->upperFirst($oFields->middleName),
            true
        ) : '';

        $sCountry = !empty($oFields->country) ? $oFields->country : '';
        $sCity = !empty($oFields->city) ? $this->str->escape($this->str->upperFirst($oFields->city), true) : '';
        $sState = !empty($oFields->state) ? $this->str->escape($this->str->upperFirst($oFields->state), true) : '';
        $sPunchline = !empty($oFields->punchline) ? $this->str->escape(Ban::filterWord($oFields->punchline)) : '';
        $sDescription = !empty($oFields->description) ? Emoticon::init(Ban::filterWord($oFields->description)) : '';

        return [
            'first_name' => $sFirstName,
            'last_name' => $sLastName,
            'middle_name' => $sMiddleName,
            'country' => $sCountry,
            'city' => $sCity,
            'state' => $sState,
            'punchline' => $sPunchline,
            'description' => $sDescription,
            'age' => UserBirthDateCore::getAgeFromBirthDate($oUser->birthDate)
        ];
    }

    /**
     * Add JS file for the Ajax Friend Adder feature.
     */
    protected function addAdditionalAssetFiles(): void
    {
        if (SysMod::isEnabled('friend')) {
            $this->design->addJs(
                PH7_LAYOUT . PH7_SYS . PH7_MOD . 'friend' . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
                'friend.js'
            );
        }
    }

    /**
     * Set noindex meta tag to exclude the profile from search engines.
     */
    protected function excludeProfileFromSearchEngines(): void
    {
        $this->view->header .= Meta::NOINDEX;
    }

    /**
     * Enable the social meta tags (FB, Twitter, ...) with the profile photo.
     */
    protected function imageToSocialMetaTags(stdClass $oUser): void
    {
        $sAvatarImageUrl = $this->design->getUserAvatar(
            $oUser->username,
            $oUser->sex,
            self::SOCIAL_TAG_AVATAR_SIZE,
            false
        );
        $this->view->image_social_meta_tag = $sAvatarImageUrl;
    }

    /**
     * @param int One the these consts: FriendCoreModel::APPROVED_REQUEST, FriendCoreModel::PENDING_REQUEST, FriendCoreModel::ALL_REQUEST
     */
    private function isFriend(int $iStatus = FriendCoreModel::ALL_REQUEST): bool
    {
        return (new FriendCoreModel)->inList(
            $this->iVisitorId,
            $this->iProfileId,
            $iStatus
        );
    }

    /**
     * Update the "Who's Viewed Your Profile"
     *
     * @param stdClass $oUserPrivacyViews
     */
    private function updateProfileViews(stdClass $oUserPrivacyViews): void
    {
        $oVisitor = new VisitorCore($this);
        $oVisitorPrivacyViews = $this->oUserModel->getPrivacySetting($this->iVisitorId);

        if ($oVisitor->isViewUpdateEligible($oUserPrivacyViews, $oVisitorPrivacyViews)) {
            $oVisitor->updateViews();
        }
        unset($oVisitorPrivacyViews);
    }
}
