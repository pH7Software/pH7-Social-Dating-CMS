<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Math\Measure\Year;
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
    const MAP_ZOOM_LEVEL = 10;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '300px';

    /** @var int */
    protected $iProfileId;

    /** @var int */
    protected $iVisitorId;

    /** @var bool */
    protected $bUserAuth;

    /**
     * Index displaying the user profile page.
     *
     * @return void
     */
    abstract public function index();

    /**
     * Privacy Profile.
     *
     * @param UserCoreModel $oUserModel
     * @param stdClass $oUser
     *
     * @return void
     *
     * @throws Framework\File\Exception
     */
    protected function initPrivacy(UserCoreModel $oUserModel, stdClass $oUser)
    {
        // Check Privacy Profile
        $oPrivacyViewsUser = $oUserModel->getPrivacySetting($this->iProfileId);

        if ($oPrivacyViewsUser->searchProfile === 'no') {
            $this->excludeProfileFromSearchEngines();
        }

        if (!$this->bUserAuth && $oPrivacyViewsUser->privacyProfile === 'only_members') {
            $this->view->error = t('Whoops! "%0%" profile is only visible to members. Please <a href="%1%">login</a> or <a href="%2%">register</a> to see this profile.',
                $oUser->username, Uri::get('user', 'main', 'login'), Uri::get('user', 'signup', 'step1'));
        } elseif ($oPrivacyViewsUser->privacyProfile === 'only_me' && !$this->isOwnProfile()) {
            $this->view->error = t('Whoops! "%0%" profile is not available to you.', $oUser->username);
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
     * @param string $sFirstName
     * @param stdClass $oUser
     *
     * @return void
     */
    protected function setMenuBar($sFirstName, stdClass $oUser)
    {
        $this->view->mail_link = $this->getMailLink($sFirstName, $oUser);
        $this->view->messenger_link = $this->getMessengerLink($sFirstName, $oUser);
        $this->view->befriend_link = $this->getBeFriendLink($sFirstName, $oUser);
    }

    /**
     * Set the Google Maps code to the view.
     *
     * @param string $sCity
     * @param string $sCountry
     * @param stdClass $oUser
     *
     * @return void
     */
    protected function setMap($sCity, $sCountry, stdClass $oUser)
    {
        $sFullAddress = $sCity . ' ' . t($sCountry);
        $sMarkerText = t('Meet <b>%0%</b> near here!', $oUser->username);
        $oMap = new Map;
        $oMap->setKey(DbConfig::getSetting('googleApiKey'));
        $oMap->setCenter($sFullAddress);
        $oMap->setSize(static::MAP_WIDTH_SIZE, static::MAP_HEIGHT_SIZE);
        $oMap->setDivId('profile_map');
        $oMap->setZoom(static::MAP_ZOOM_LEVEL);
        $oMap->addMarkerByAddress($sFullAddress, $sMarkerText, $sMarkerText);
        $oMap->generate();
        $this->view->map = $oMap->getMap();
        unset($oMap);
    }

    /**
     * @param string $sFirstName User's first name.
     * @param stdClass $oUser User data from the DB.
     *
     * @return string The anchor for the link.
     */
    protected function getMailLink($sFirstName, stdClass $oUser)
    {
        if ($this->bUserAuth) {
            $sMailLink = Uri::get('mail', 'main', 'compose', $oUser->username);
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
    protected function getMessengerLink($sFirstName, stdClass $oUser)
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
    protected function getBeFriendLink($sFirstName, stdClass $oUser)
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
    protected function getFilteredData(stdClass $oUser, stdClass $oFields)
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
     * @return bool TRUE if the user is on their own profile, FALSE otherwise.
     */
    protected function isOwnProfile()
    {
        return $this->str->equals($this->iVisitorId, $this->iProfileId);
    }

    /**
     * Add JS file for the Ajax Friend Adder feature.
     *
     * @return void
     */
    protected function addAdditionalAssetFiles()
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
     *
     * @return void
     */
    protected function excludeProfileFromSearchEngines()
    {
        $this->view->header = Meta::NOINDEX;
    }

    /**
     * @return void
     */
    private function updateVisitorViews()
    {
        $oVisitorModel = new VisitorCoreModel(
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
}
