<?php
/**
 * @title          Profile Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 * @version        1.4
 */
namespace PH7;
use
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Analytics\Statistic,
PH7\Framework\Parse\Emoticon,
PH7\Framework\Security\Ban\Ban,
PH7\Framework\Url\Url,
PH7\Framework\Geo\Map\Map,
PH7\Framework\Date\Various as VDate;

class ProfileController extends Controller
{

    private $sTitle;

    public function index()
    {
        $oUserModel = new UserModel;

        // Add the style sheet for the Tabs Menu
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_CSS, 'tabs.css');
        // Add the JavaScript file for the Ajax Friend
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_DS . PH7_TPL . PH7_TPL_MOD_NAME . PH7_DS . PH7_JS, 'friend.js');

        $sGetUsername = $this->httpRequest->get('username', 'string');
        $iGetProfileId = $oUserModel->getId(null, $sGetUsername);

        $oUser = $oUserModel->readProfile($iGetProfileId);

        if (!empty($oUser->username) && $this->str->equalsIgnoreCase($sGetUsername, $oUser->username))
        {
            // Get the Profile's ID and the Visitor's ID.
            $iId = (int) $oUser->profileId;
            $iMemberId = (int) $this->session->get('member_id');

            // Get the Profile's username
            $sUsername = $this->str->escape($oUser->username, true);

            // The administrators can view all profiles and profile visits are not saved.
            if (!AdminCore::auth())
                $this->_initPrivacy($oUserModel, $iId, $iMemberId, $sUsername);

            // Gets the Profile's background
            $this->view->img_background = $oUserModel->getBackground($iId, 1);

            $oFields = $oUserModel->getInfoFields($iId);

            unset($oUserModel);

            $sFirstName = (!empty($oUser->firstName)) ? $this->str->escape($this->str->upperFirst($oUser->firstName), true) : '';
            $sLastName = (!empty($oUser->lastName)) ? $this->str->escape($this->str->upperFirst($oUser->lastName), true) : '';

            $sCountry = (!empty($oFields->country)) ? $oFields->country : '';
            $sCity = (!empty($oFields->city)) ? $this->str->escape($this->str->upperFirst($oFields->city), true) : '';
            $sState = (!empty($oFields->state)) ? $this->str->escape($this->str->upperFirst($oFields->state), true) : '';
            $sDescription = (!empty($oFields->description)) ? Emoticon::init(Ban::filterWord($oFields->description)) : '';

            // Age
            $this->view->birth_date = $this->dateTime->get($oUser->birthDate)->date();
            $aAge = explode('-', $oUser->birthDate);
            $iAge = (new Framework\Math\Measure\Year($aAge[0], $aAge[1], $aAge[2]))->get();

            // Links of the Menubar
            $iNbFriend = FriendModel::totalFriends($iId);
            $sNbFriend = ($iNbFriend > 0) ? ' (' . $iNbFriend . ')' : '';
            $sFriendTxt = ($iNbFriend <= 1) ? ($iNbFriend == 1) ? t('Friend:') : t('No Friends') :
                t('Friends:');

            if (User::auth())
            {
                $iNbMutFriend = (new FriendModel)->get($iMemberId, $iId, null, true, null, null, null, null);
                $sNbMutFriend = ($iNbMutFriend > 0) ? ' (' . $iNbMutFriend . ')' : '';
                $sMutFriendTxt = ($iNbMutFriend <= 1) ? ($iNbMutFriend == 1) ? t('Mutual Friend:') : t('No Mutual Friends') : t('Mutuals Friends:');
            }

            $sMailLink = (User::auth()) ?
                UriRoute::get('mail', 'main', 'compose', $sUsername) :
                UriRoute::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery(array('msg' => t('You need to free register for send a message to %0%.', $sFirstName),
                'ref' => 'profile', 'a' => 'mail', 'u' => $sUsername, 'f_n' => $sFirstName, 's' => $oUser->sex)), false);
            $sMessengerLink = (User::auth()) ?
                'javascript:void(0)" onclick="Messenger.chatWith(\'' . $sUsername . '\')' :
                UriRoute::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery(array('msg' => t('You need to free register for talk to %0%.', $sFirstName),
                'ref' => 'profile', 'a' => 'messenger', 'u' => $sUsername, 'f_n' => $sFirstName, 's' => $oUser->sex)), false);
            $sBefriendLink = (User::auth()) ?
                'javascript:void(0)" onclick="friend(\'add\',' . $iId . ',\''.(new Framework\Security\CSRF\Token)->generate('friend').'\')' :
                UriRoute::get('user', 'signup', 'step1', '?' . Url::httpBuildQuery(array('msg' => t('Free Sign up for %site_name% to become friend with %0%.', $sFirstName), 'ref' => 'profile', 'a' => 'befriend&', 'u' => $sUsername, 'f_n' => $sFirstName, 's' => $oUser->sex)), false);

            $this->view->page_title = t('Meet %0%, A beautiful %1% looking some %2% - %3% years - %4% - %5% %6%',
                $sFirstName, t($oUser->sex), t($oUser->matchSex), $iAge, t($sCountry), $sCity, $sState);
            $this->view->meta_description = t('Meet %0% %1% | %2% - %3%', $sFirstName, $sLastName,
                $sUsername, substr($sDescription, 0, 100));
            $this->view->h1_title = t('Meet <span class="pH1">%0%</span> on <span class="pH0">%site_name%</span>',
                $sFirstName);
            $this->view->h2_title = t('A <span class="pH1">%0%</span> of <span class="pH3">%1% years</span>, from <span class="pH2">%2%, %3% %4%</span>',
                t($oUser->sex), $iAge, t($sCountry), $sCity, $sState);


            $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

            // Member Menubar
            $this->view->friend_link = $sFriendTxt . $sNbFriend;
            if (User::auth()) $this->view->mutual_friend_link = $sMutFriendTxt . $sNbMutFriend;
            $this->view->mail_link = $sMailLink;
            $this->view->messenger_link = $sMessengerLink;
            $this->view->befriend_link = $sBefriendLink;

            // Set parameters Google Map
            $oMap = new Map;
            $oMap->setCenter($sCity . ' ' . $sState . ' ' . t($sCountry));
            $oMap->setSize('600px', '300px');
            $oMap->setDivId('profileMap');
            $oMap->setZoom(12);
            $oMap->addMarkerByAddress($sCity . ' ' . $sState . ' ' . t($sCountry), t('Meet %0% near here!', $sUsername));
            $oMap->generate();
            $this->view->map = $oMap->getMap();
            unset($oMap);

            $this->view->id = $iId;
            $this->view->member_id = $iMemberId;
            $this->view->username = $sUsername;
            $this->view->first_name = $sFirstName;
            $this->view->last_name = $sLastName;
            $this->view->sex = $oUser->sex;
            $this->view->match_sex = $oUser->matchSex;
            $this->view->age = $iAge;
            $this->view->country = t($sCountry);
            $this->view->country_code = $sCountry;
            $this->view->city = $sCity;
            $this->view->state = $sState;
            $this->view->description = nl2br($sDescription);
            $this->view->join_date = VDate::textTimeStamp($oUser->joinDate);
            $this->view->last_activity = VDate::textTimeStamp($oUser->lastActivity);
            $this->view->fields = $oFields;

            // Stat Profile
            Statistic::setView($iId, 'Members');

        }
        else
        {
            Framework\Http\Http::setHeadersByCode(404);
            $this->sTitle = t('Whoops! The profile "%0%" is not found. ', $this->str->
                escape(substr($sGetUsername, 0, PH7_MAX_USERNAME_LENGTH), true));
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->error = '<strong><i>' . t('Suggestions:') . '</i></strong><br />' .
                t('<a href="javascript:history.back();">Go back to the previous page</a><br />');
        }

        $this->output();
    }

    /**
     * Privacy Profile.
     *
     * @param object \PH7\UserModel $oUserModel
     * @param integer $iId Profile's ID.
     * @param integer $oMemberId Visitor's ID.
     * @param string $sUsername Username's Profile.
     * @return void
     */
    private function _initPrivacy(UserModel $oUserModel, $iId, $iMemberId, $sUsername)
    {
        // Check Privacy Profile
        $oPrivacyViewsUser = $oUserModel->getPrivacySetting($iId);

        if (!User::auth() && $oPrivacyViewsUser->privacyProfile == 'only_members')
        {
            $this->view->error = t('Whoops! The "%0%" profile is only visible to members. Please <a href="%1%">login</a> or <a href="%2%">register</a> to view this profile.',
                $sUsername, UriRoute::get('user', 'main', 'login'), UriRoute::get('user',
                'signup', 'step1'));
        }
        elseif (User::auth() && $oPrivacyViewsUser->privacyProfile == 'only_me' && !$this->str->equals($iId, $iMemberId))
        {
            $this->view->error = t('Whoops! The "%0%" profile is not available to you.', $sUsername);
        }

        // Update the "Who's Viewed Your Profile"
        if (User::auth())
        {
            $oPrivacyViewsVisitor = $oUserModel->getPrivacySetting($iMemberId);

            if ($oPrivacyViewsUser->userSaveViews == 'yes' && $oPrivacyViewsVisitor->userSaveViews == 'yes' && !$this->str->equals($iId, $iMemberId))
            {
                $oVisitorModel = new VisitorModel($iId, $iMemberId, $this->dateTime->get()->dateTime('Y-m-d H:i:s'));

                if (!$oVisitorModel->already())
                {
                    // Add a new visit
                    $oVisitorModel->set();
                }
                else
                {
                    // Update the date of last visit
                    $oVisitorModel->update();
                }
                unset($oVisitorModel);
            }
        }
        unset($oPrivacyViewsUser, $oPrivacyViewsVisitor);
    }

}
