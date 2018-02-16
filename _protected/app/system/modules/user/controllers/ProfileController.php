<?php
/**
 * @title          Profile Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Date\Various as VDate;
use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Http\Http;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Math\Measure\Year;
use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;
use PH7\Framework\Url\Url;
use stdClass;

class ProfileController extends Controller
{
    const MAP_ZOOM_LEVEL = 12;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '300px';

    /** @var bool */
    private $bUserAuth;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iProfileId;

    /** @var int */
    private $iVisitorId;

    public function __construct()
    {
        parent::__construct();

        $this->bUserAuth = User::auth();
    }

    public function index()
    {
        $oUserModel = new UserModel;

        $this->addCssFiles();
        $this->addAdditionalAssetFiles();

        // Set the Profile username
        $this->sUsername = $this->httpRequest->get('username', 'string');

        // Set the Profile ID and Visitor ID
        $this->iProfileId = $oUserModel->getId(null, $this->sUsername);
        $this->iVisitorId = (int)$this->session->get('member_id');

        // Read the Profile information
        $oUser = $oUserModel->readProfile($this->iProfileId);

        if ($oUser && $this->doesProfileExist($oUser)) {
            $this->redirectToOtherProfileStyleIfEnabled();

            // The administrators can view all profiles and profile visits are not saved.
            if (!AdminCore::auth() || UserCore::isAdminLoggedAs()) {
                $this->initPrivacy($oUserModel);
            }

            // Gets the Profile background
            $this->view->img_background = $oUserModel->getBackground($this->iProfileId, 1);

            $oFields = $oUserModel->getInfoFields($this->iProfileId);

            unset($oUserModel);

            $sFirstName = !empty($oUser->firstName) ? $this->str->escape($this->str->upperFirst($oUser->firstName), true) : '';
            $sLastName = !empty($oUser->lastName) ? $this->str->escape($this->str->upperFirst($oUser->lastName), true) : '';
            $sMiddleName = !empty($oFields->middleName) ? $this->str->escape($this->str->upperFirst($oFields->middleName), true) : '';

            $sCountry = !empty($oFields->country) ? $oFields->country : '';
            $sCity = !empty($oFields->city) ? $this->str->escape($this->str->upperFirst($oFields->city), true) : '';
            $sState = !empty($oFields->state) ? $this->str->escape($this->str->upperFirst($oFields->state), true) : '';
            $sDescription = !empty($oFields->description) ? Emoticon::init(Ban::filterWord($oFields->description)) : '';

            // Age
            $this->view->birth_date = $oUser->birthDate;
            $this->view->birth_date_formatted = $this->dateTime->get($oUser->birthDate)->date();
            $aAge = explode('-', $oUser->birthDate);
            $iAge = (new Year($aAge[0], $aAge[1], $aAge[2]))->get();

            $this->view->page_title = t('Meet %0%, A %1% looking for %2% - %3% years - %4% - %5% %6%',
                $sFirstName, t($oUser->sex), t($oUser->matchSex), $iAge, t($sCountry), $sCity, $sState);

            $this->view->meta_description = t('Meet %0% %1% | %2% - %3%', $sFirstName, $sLastName,
                $oUser->username, substr($sDescription, 0, 100));

            $this->view->h1_title = t('Meet <span class="pH1">%0%</span> on <span class="pH0">%site_name%</span>',
                $sFirstName);

            $this->view->h2_title = t('A <span class="pH1">%0%</span> of <span class="pH3">%1% years</span>, from <span class="pH2">%2%, %3% %4%</span>',
                t($oUser->sex), $iAge, t($sCountry), $sCity, $sState);

            // Member Menubar
            $this->view->mail_link = $this->getMailLink($sFirstName, $oUser);
            $this->view->messenger_link = $this->getMessengerLink($sFirstName, $oUser);

            if (SysMod::isEnabled('friend')) {
                $this->view->friend_link = $this->getFriendLinkName();

                if ($this->bUserAuth) {
                    $this->view->mutual_friend_link = $this->getMutualFriendLinkName();
                }

                $this->view->befriend_link = $this->getBeFriendLink($sFirstName, $oUser);
            }

            $this->view->map = $this->getMap($sCity, $sCountry, $oUser);

            $this->view->id = $this->iProfileId;
            $this->view->username = $oUser->username;
            $this->view->first_name = $sFirstName;
            $this->view->last_name = $sLastName;
            $this->view->middle_name = $sMiddleName;
            $this->view->sex = $oUser->sex;
            $this->view->match_sex = $oUser->matchSex;
            $this->view->match_sex_search = str_replace(array('[code]', ','), '&sex[]=', '[code]' . $oUser->matchSex);
            $this->view->age = $iAge;
            $this->view->country = t($sCountry);
            $this->view->country_code = $sCountry;
            $this->view->city = $sCity;
            $this->view->state = $sState;
            $this->view->description = nl2br($sDescription);
            $this->view->join_date = VDate::textTimeStamp($oUser->joinDate);
            $this->view->last_activity = VDate::textTimeStamp($oUser->lastActivity);
            $this->view->fields = $oFields;
            $this->view->is_logged = $this->bUserAuth;
            $this->view->is_own_profile = $this->isOwnProfile();

            // Count number of views
            Statistic::setView($this->iProfileId, DbTableName::MEMBER);
        } else {
            $this->notFound();
        }

        $this->output();
    }

    /**
     * Get the Google Map.
     *
     * @param string $sCity
     * @param string $sCountry
     * @param stdClass $oUser
     *
     * @return string The Google Maps code.
     */
    private function getMap($sCity, $sCountry, stdClass $oUser)
    {
        $sFullAddress = $sCity . ' ' . t($sCountry);
        $sMarkerText = t('Meet <b>%0%</b> near here!', $oUser->username);

        $oMap = new Map;
        $oMap->setKey(DbConfig::getSetting('googleApiKey'));
        $oMap->setCenter($sFullAddress);
        $oMap->setSize(self::MAP_WIDTH_SIZE, self::MAP_HEIGHT_SIZE);
        $oMap->setDivId('profile_map');
        $oMap->setZoom(self::MAP_ZOOM_LEVEL);
        $oMap->addMarkerByAddress($sFullAddress, $sMarkerText, $sMarkerText);
        $oMap->generate();
        $sMap = $oMap->getMap();
        unset($oMap);

        return $sMap;
    }

    /**
     * Privacy Profile.
     *
     * @param UserCoreModel $oUserModel
     *
     * @return void
     *
     * @throws Framework\File\Exception
     */
    private function initPrivacy(UserModel $oUserModel)
    {
        // Check Privacy Profile
        $oPrivacyViewsUser = $oUserModel->getPrivacySetting($this->iProfileId);

        if ($oPrivacyViewsUser->searchProfile === 'no') {
            $this->excludeProfileFromSearchEngines();
        }

        if (!$this->bUserAuth && $oPrivacyViewsUser->privacyProfile === 'only_members') {
            $this->view->error = t('Whoops! "%0%" profile is only visible to members. Please <a href="%1%">login</a> or <a href="%2%">register</a> to see this profile.',
                $this->sUsername, Uri::get('user', 'main', 'login'), Uri::get('user', 'signup', 'step1'));
        } elseif ($oPrivacyViewsUser->privacyProfile === 'only_me' && !$this->isOwnProfile()) {
            $this->view->error = t('Whoops! "%0%" profile is not available to you.', $this->sUsername);
        }

        // Update the "Who's Viewed Your Profile"
        if ($this->bUserAuth) {
            $oPrivacyViewsVisitor = $oUserModel->getPrivacySetting($this->iVisitorId);

            if ($oPrivacyViewsUser->userSaveViews === 'yes' &&
                $oPrivacyViewsVisitor->userSaveViews === 'yes' &&
                !$this->isOwnProfile()
            ) {
                $this->updateVisitorViews();
            }
        }
        unset($oPrivacyViewsUser, $oPrivacyViewsVisitor);
    }

    /**
     * @return void
     */
    private function updateVisitorViews()
    {
        $oVisitorModel = new VisitorModel(
            $this->iProfileId,
            $this->iVisitorId,
            $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        );

        if (!$oVisitorModel->already()) {
            // Add a new visit
            $oVisitorModel->set();
        } else {
            // Update the date of last visit
            $oVisitorModel->update();
        }
        unset($oVisitorModel);
    }

    /**
     * @return bool TRUE if the user is on their own profile, FALSE otherwise.
     */
    private function isOwnProfile()
    {
        return $this->str->equals($this->iVisitorId, $this->iProfileId);
    }

    /**
     * @param string $sFirstName User's first name.
     * @param stdClass $oUser User data from the DB.
     *
     * @return string The anchor for the link.
     */
    private function getMailLink($sFirstName, stdClass $oUser)
    {
        if ($this->bUserAuth) {
            $sMailLink = Uri::get('mail', 'main', 'compose', $oUser->username);
        } else {
            $aUrlParms = [
                'msg' => t('Register for free in order to message %0%.', $sFirstName),
                'ref' => 'profile',
                'a' => 'mail',
                'u' => $oUser->username,
                'f_n' => $sFirstName,
                's' => $oUser->sex
            ];
            $sMailLink = Uri::get(
                'user',
                'signup',
                'step1', '?' . Url::httpBuildQuery($aUrlParms),
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
    private function getMessengerLink($sFirstName, stdClass $oUser)
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
     * @return string The anchor for the link.
     */
    private function getBeFriendLink($sFirstName, stdClass $oUser)
    {
        if ($this->bUserAuth) {
            $sBefriendLink = 'javascript:void(0)" onclick="friend(\'add\',' . $this->iProfileId . ',\'' . (new Token)->generate('friend') . '\')';
        } else {
            $aUrlParms = [
                'msg' => t('Free Sign up for %site_name% to become friend with %0%.', $sFirstName),
                'ref' => 'profile',
                'a' => 'befriend',
                'u' => $oUser->username,
                'f_n' => $sFirstName,
                's' => $oUser->sex
            ];
            $sBefriendLink = Uri::get(
                'user',
                'signup',
                'step1', '?' . Url::httpBuildQuery($aUrlParms),
                false
            );
        }

        return $sBefriendLink;
    }

    /**
     * @return string
     */
    private function getFriendLinkName()
    {
        $iNbFriend = FriendCoreModel::total($this->iProfileId);
        $sNbFriend = $iNbFriend > 0 ? ' (' . $iNbFriend . ')' : '';
        $sFriendTxt = $iNbFriend <= 1 ? ($iNbFriend == 1) ? t('Friend:') : t('No Friends') : t('Friends:');

        return $sFriendTxt . $sNbFriend;
    }

    /**
     * @return string
     */
    private function getMutualFriendLinkName()
    {
        $iNbMutFriend = (new FriendCoreModel)->get(
            $this->iVisitorId,
            $this->iProfileId,
            null,
            true,
            null,
            null,
            null,
            null
        );
        $sNbMutFriend = $iNbMutFriend > 0 ? ' (' . $iNbMutFriend . ')' : '';
        $sMutFriendTxt = $iNbMutFriend <= 1 ? ($iNbMutFriend == 1) ? t('Mutual Friend:') : t('No Mutual Friends') : t('Mutual Friends:');

        return $sMutFriendTxt . $sNbMutFriend;
    }

    /**
     * @param stdClass $oUser
     *
     * @return bool
     */
    private function doesProfileExist(stdClass $oUser)
    {
        return !empty($oUser->username) && $this->str->equalsIgnoreCase($this->sUsername, $oUser->username);
    }

    /**
     * Set noindex meta tag to exclude the profile from search engines.
     *
     * @return void
     */
    private function excludeProfileFromSearchEngines()
    {
        $this->view->header = Meta::NOINDEX;
    }

    /**
     * @return void
     *
     * @throws Framework\File\Exception
     */
    private function redirectToOtherProfileStyleIfEnabled()
    {
        if (SysMod::isEnabled('cool-profile-page')) {
            // If enabled, redirect to the other profile page style
            Header::redirect(
                Uri::get('cool-profile-page', 'main', 'index', $this->iProfileId)
            );
        }
    }

    /**
     * Add the General and Tabs Menu stylesheets.
     *
     * @return void
     */
    private function addCssFiles()
    {
        $this->design->addCss(
            PH7_LAYOUT,
            PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS . 'tabs.css,' . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS . 'general.css'
        );
    }

    /**
     * Add the JS file for Friend feature.
     *
     * @return void
     */
    private function addAdditionalAssetFiles()
    {
        if (SysMod::isEnabled('friend')) {
            $this->design->addJs(
                PH7_LAYOUT . PH7_SYS . PH7_MOD . 'friend' . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
                'friend.js'
            );
        }
    }

    /**
     * Show a Not Found page.
     *
     * @return void
     *
     * @throws Framework\Http\Exception
     */
    private function notFound()
    {
        Http::setHeadersByCode(self::HTTP_NOT_FOUND_CODE);

        /**
         * @internal We can include HTML tags in the title since the template will automatically escape them before displaying it.
         */
        $this->sTitle = t('Whoops! "%0%" profile is not found.', substr($this->sUsername, 0, PH7_MAX_USERNAME_LENGTH), true);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = '<strong><em>' . t('Suggestions:') . '</em></strong><br />
            <a href="' . $this->registry->site_url . '">' . t('Return home') . '</a><br />
            <a href="javascript:history.back();">' . t('Go back to the previous page') . '</a><br />';
    }
}
