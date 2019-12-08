<?php
/**
 * @title          Profile Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Cool Profile Page / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Date\Various as VDate;
use PH7\Framework\Module\Various as SysMod;

class MainController extends ProfileBaseController
{
    const MAP_ZOOM_LEVEL = 10;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '200px';

    public function __construct()
    {
        parent::__construct();

        // Set the Profile ID and Visitor ID
        $this->iProfileId = $this->httpRequest->get('profile_id', 'int');
        $this->iVisitorId = (int)$this->session->get('member_id');
    }

    public function index()
    {
        $this->addCssFiles();
        $this->addAdditionalAssetFiles();

        // Read the profile information
        $oUser = $this->oUserModel->readProfile($this->iProfileId);
        if ($oUser) {
            // The administrators can view all profiles and profile visits are not saved.
            if (!AdminCore::auth() || UserCore::isAdminLoggedAs()) {
                $this->initPrivacy($oUser);
            }

            // Assign the profile background image to the view
            $this->view->img_background = $this->oUserModel->getBackground($this->iProfileId, 1);

            $oFields = $this->oUserModel->getInfoFields($this->iProfileId);

            // Date of birth
            $this->view->birth_date = $oUser->birthDate;
            $this->view->birth_date_formatted = $this->dateTime->get($oUser->birthDate)->date();

            $aData = $this->getFilteredData($oUser, $oFields);

            $this->view->page_title = t('Meet %0%. A %1% looking for %2% - %3% years - %4% - %5% %6%',
                $aData['first_name'], t($oUser->sex), t($oUser->matchSex), $aData['age'], t($aData['country']), $aData['city'], $aData['state']);

            $this->view->meta_description = t('Meet %0% %1% | %2% - %3%', $aData['first_name'], $aData['last_name'],
                $oUser->username, substr($aData['description'], 0, 100));

            $this->view->h1_title = t('A <span class="pH1">%0%</span> of <span class="pH3">%1% years</span>, from <span class="pH2">%2%, %3% %4%</span>',
                t($oUser->sex), $aData['age'], t($aData['country']), $aData['city'], $aData['state']);

            $this->imageToSocialMetaTags($oUser);
            $this->setMenuBar($aData['first_name'], $oUser);

            if (SysMod::isEnabled('map')) {
                $this->setMap($aData['city'], $aData['country'], $oUser);
            }

            $this->view->id = $this->iProfileId;
            $this->view->visitor_id = $this->iVisitorId;
            $this->view->username = $oUser->username;
            $this->view->first_name = $aData['first_name'];
            $this->view->last_name = $aData['last_name'];
            $this->view->middle_name = $aData['middle_name'];
            $this->view->sex = $oUser->sex;
            $this->view->match_sex = $oUser->matchSex;
            $this->view->match_sex_search = str_replace(['[code]', ','], '&amp;sex[]=', '[code]' . $oUser->matchSex);
            $this->view->age = $aData['age'];
            $this->view->country = t($aData['country']);
            $this->view->country_code = $aData['country'];
            $this->view->city = $aData['city'];
            $this->view->state = $aData['state'];
            $this->view->punchline = $aData['punchline'];
            $this->view->description = nl2br($aData['description']);
            $this->view->join_date = VDate::textTimeStamp($oUser->joinDate);
            $this->view->last_activity = VDate::textTimeStamp($oUser->lastActivity);
            $this->view->fields = $oFields;
            $this->view->is_logged = $this->bUserAuth;
            $this->view->is_own_profile = $this->isOwnProfile();

            // Count number of times the profile is viewed
            Statistic::setView($this->iProfileId, DbTableName::MEMBER);
        } else {
            $this->displayPageNotFound();
        }

        $this->output();
    }

    /**
     * Add the General and Tabs Menu stylesheets.
     *
     * @return void
     */
    protected function addCssFiles()
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'style.css'
        );
    }
}
