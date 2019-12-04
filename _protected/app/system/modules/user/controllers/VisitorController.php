<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Navigation\Page;

class VisitorController extends Controller
{
    const MAX_PROFILE_PER_PAGE = 10;

    /** @var UserModel */
    private $oUserModel;

    /** @var VisitorModel */
    private $oVisitorModel;

    /** @var Page */
    private $oPage;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iId;

    /** @var int */
    private $iTotalVisitors;

    public function __construct()
    {
        parent::__construct();

        $this->sUsername = $this->getUsername();

        /**
         * FIRST USERNAME LETTER IN UPPERCASE
         * We can do this because the SQL search is case insensitive.
         * Be careful not to do this if you need this username in the method \PH7\Framework\Layout\Html::getUserAvatar()
         * since it won't find the user folder because it is case-sensitive.
         */
        $this->sUsername = $this->str->upperFirst($this->sUsername);

        $this->view->username = $this->sUsername;

        $this->oUserModel = new UserModel;
        $this->iId = $this->oUserModel->getId(null, $this->sUsername);

        $this->oVisitorModel = new VisitorModel($this->iId);
        $this->oPage = new Page;

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        /**
         *  Predefined meta_description.
         */
        $this->view->meta_description = t("The Last %0%'s visitors. Meet new people and make new visitors on your social profile. Make new Visitors and Friends with %site_name%", $this->sUsername);

        /**
         *  Predefined meta_keywords tags.
         */
        $this->view->meta_keywords = t('visitor,friend,dating,social networking,visitors,spy,profile,social,%0%', $this->sUsername);
    }

    public function index()
    {
        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalVisitors, self::MAX_PROFILE_PER_PAGE);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->iTotalVisitors = $this->oVisitorModel->get(
            $this->httpRequest->get('looking'),
            true,
            SearchCoreModel::LAST_VISIT,
            SearchCoreModel::DESC,
            null,
            null
        );
        $oVisitor = $this->oVisitorModel->get(
            $this->httpRequest->get('looking'),
            false,
            SearchCoreModel::LAST_VISIT,
            SearchCoreModel::DESC,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->view->user_views_setting = UserCore::auth() ? $this->oUserModel->getPrivacySetting($this->session->get('member_id'))->userSaveViews : '';

        if (empty($oVisitor)) {
            $this->sTitle = t('No one has seen "%0%"', $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->error = t('No one has visited the profile.');
        } else {
            $this->sTitle = t("%0%'s Visitors:", $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $sVisitorTxt = nt('%n% Visitor', '%n% Visitors', $this->iTotalVisitors);
            $this->view->visitor_number = $sVisitorTxt;
            $this->view->visitors = $oVisitor;
        }

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t("Find someone who has visited %0%'s profile", $this->sUsername);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    /**
     * If the user is logged in, get 'member_username' session, otherwise get username from URL.
     *
     * @return string
     */
    private function getUsername()
    {
        if (!$this->httpRequest->getExists('username')) {
            return $this->session->get('member_username');
        }

        return $this->httpRequest->get('username');
    }
}
