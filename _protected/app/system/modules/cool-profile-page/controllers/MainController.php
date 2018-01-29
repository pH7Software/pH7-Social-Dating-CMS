<?php
/**
 * @title          Profile Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Cool Profile Page / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Date\Various as VDate;
use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Math\Measure\Year;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Url;
use stdClass;

class MainController extends Controller
{
    const MAP_ZOOM_LEVEL = 10;
    const MAP_WIDTH_PIXEL = 200;

    /** @var bool */
    private $bUserAuth;

    /** @var string */
    private $sUsername;

    /** @var int */
    private $iProfileId;

    /** @var int */
    private $iVisitorId;

    public function __construct()
    {
        parent::__construct();

        $this->bUserAuth = UserCore::auth();
    }

    public function index()
    {
        $oUserModel = new UserCoreModel;

        // Add the General and Tabs Menu stylesheets
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'style.css'
        );

        $this->iProfileId = $this->httpRequest->get('profile_id', 'int');

        // Read the profile information
        $oUser = $oUserModel->readProfile($this->iProfileId);
        if ($oUser) {
            // The administrators can view all profiles and profile visits are not saved.
            if (!AdminCore::auth() || UserCore::isAdminLoggedAs()) {
                $this->initPrivacy($oUserModel, $this->iProfileId, $this->iVisitorId);
            }

            // Gets the Profile background
            $this->view->img_background = $oUserModel->getBackground($this->iProfileId, 1);

            $oFields = $oUserModel->getInfoFields($this->iProfileId);

            unset($oUserModel);

            // Age
            $this->view->birth_date = $oUser->birthDate;
            $this->view->birth_date_formatted = $this->dateTime->get($oUser->birthDate)->date();

            $aData = $this->getFilteredData();

            $this->view->page_title = t('Meet %0%, A %1% looking for %2% - %3% years - %4% - %5% %6%',
                $aData['first_name'], t($oUser->sex), t($oUser->matchSex), $aData['age'], t($aData['country']), $aData['city'], $aData['state']);

            $this->view->meta_description = t('Meet %0% %1% | %2% - %3%', $aData['first_name'], $aData['last_name'],
                $oUser->username, substr($aData['description'], 0, 100));

            $this->view->h3_title = t('A <span class="pH1">%0%</span> of <span class="pH3">%1% years</span>, from <span class="pH2">%2%, %3% %4%</span>',
                t($oUser->sex), $aData['age'], t($aData['country']), $aData['city'], $aData['state']);

            $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

            // Member Menubar
            $this->view->mail_link = $this->getMailLink($aData['first_name'], $oUser);
            $this->view->messenger_link = $this->getMessengerLink($aData['first_name'], $oUser);
            $this->view->befriend_link = $this->getBeFriendLink($aData['first_name'], $oUser);

            // Set parameters Google Map
            $this->view->map = $this->getMap($aData['city'], $aData['state'], $aData['country'], $oUser);

            $this->view->id = $this->iProfileId;
            $this->view->username = $oUser->username;
            $this->view->first_name = $aData['first_name'];
            $this->view->last_name = $aData['last_name'];
            $this->view->middle_name = $aData['middle_name'];
            $this->view->sex = $oUser->sex;
            $this->view->match_sex = $oUser->matchSex;
            $this->view->match_sex_search = str_replace(array('[code]', ','), '&sex[]=', '[code]' . $oUser->matchSex);
            $this->view->age = $aData['age'];
            $this->view->country = t($aData['country']);
            $this->view->country_code = $aData['country'];
            $this->view->city = $aData['city'];
            $this->view->state = $aData['state'];
            $this->view->description = nl2br($aData['description']);
            $this->view->join_date = VDate::textTimeStamp($oUser->joinDate);
            $this->view->last_activity = VDate::textTimeStamp($oUser->lastActivity);
            $this->view->fields = $oFields;
            $this->view->is_logged = $this->bUserAuth;
            $this->view->is_own_profile = $this->isOwnProfile();

            // Count number of views
            Statistic::setView($this->iProfileId, 'Members');
        } else {
            $this->displayPageNotFound();
        }

        $this->output();
    }

    /**
     * Get the Google Map.
     *
     * @param string $sCity
     * @param string $sState
     * @param string $sCountry
     * @param stdClass $oUser
     *
     * @return string The Google Maps code.
     */
    private function getMap($sCity, $sState, $sCountry, stdClass $oUser)
    {
        $oMap = new Map;
        $oMap->setKey(DbConfig::getSetting('googleApiKey'));
        $oMap->setCenter($sCity . ' ' . $sState . ' ' . t($sCountry));
        $oMap->setSize('100%', self::MAP_WIDTH_PIXEL . 'px');
        $oMap->setDivId('profile_map');
        $oMap->setZoom(self::MAP_ZOOM_LEVEL);
        $oMap->addMarkerByAddress(
            $sCity . ' ' . $sState . ' ' . t($sCountry), t('Meet %0% near here!',
                $oUser->username)
        );
        $oMap->generate();
        $map = $oMap->getMap();
        unset($oMap);

        return $map;
    }

    /**
     * Privacy Profile.
     *
     * @param UserModel $oUserModel
     *
     * @return void
     */
    private function initPrivacy(UserCoreModel $oUserModel)
    {
        // Check Privacy Profile
        $oPrivacyViewsUser = $oUserModel->getPrivacySetting($this->iProfileId);

        if ($oPrivacyViewsUser->searchProfile == 'no') {
            $this->excludeProfileFromSearchEngines();
        }

        if (!$this->bUserAuth && $oPrivacyViewsUser->privacyProfile == 'only_members') {
            $this->view->error = t('Whoops! The "%0%" profile is only visible to members. Please <a href="%1%">login</a> or <a href="%2%">register</a> to see this profile.',
                $this->sUsername, Uri::get('user', 'main', 'login'), Uri::get('user', 'signup', 'step1'));
        } elseif ($oPrivacyViewsUser->privacyProfile == 'only_me' && !$this->isOwnProfile()) {
            $this->view->error = t('Whoops! The "%0%" profile is not available to you.', $this->sUsername);
        }

        // Update the "Who's Viewed Your Profile"
        if ($this->bUserAuth) {
            $oPrivacyViewsVisitor = $oUserModel->getPrivacySetting($this->iVisitorId);

            if ($oPrivacyViewsUser->userSaveViews == 'yes' &&
                $oPrivacyViewsVisitor->userSaveViews == 'yes' &&
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
                'msg' => t('You need to free register for send a message to %0%.', $sFirstName),
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
                'msg' => t('You need to free register for talk to %0%.', $sFirstName),
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
     * Returns filtered user/field data.
     *
     * @param stdClass $oUser
     * @param stdClass $oFields
     *
     * @return array
     */
    private function getFilteredData(stdClass $oUser, stdClass $oFields)
    {
        $sFirstName = !empty($oUser->firstName) ? $this->str->escape($this->str->upperFirst($oUser->firstName), true) : '';
        $sLastName = !empty($oUser->lastName) ? $this->str->escape($this->str->upperFirst($oUser->lastName), true) : '';
        $sMiddleName = !empty($oFields->middleName) ? $this->str->escape($this->str->upperFirst($oFields->middleName), true) : '';

        $sCountry = !empty($oFields->country) ? $oFields->country : '';
        $sCity = !empty($oFields->city) ? $this->str->escape($this->str->upperFirst($oFields->city), true) : '';
        $sState = !empty($oFields->state) ? $this->str->escape($this->str->upperFirst($oFields->state), true) : '';
        $sDescription = !empty($oFields->description) ? Emoticon::init(Ban::filterWord($oFields->description)) : '';


        $aAge = explode('-', $oUser->birthDate);
        $iAge = (new Year($aAge[0], $aAge[1], $aAge[2]))->get();

        return [
            'first_name' => $sFirstName,
            'last_name' => $sLastName,
            'middle_name' => $sMiddleName,
            'country' => $sCountry,
            'city' => $sCity,
            'state' => $sState,
            'description' => $sDescription,
            'age' => $iAge
        ];
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
}
