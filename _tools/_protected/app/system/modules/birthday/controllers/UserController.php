<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Birthday / Controller
 */

namespace PH7;

use PH7\Framework\Navigation\Page;

class UserController extends Controller
{
    const MAX_PROFILE_PER_PAGE = 20;

    /** @var BirthdayModel */
    private $oBirthModel;

    /** @var Page */
    private $oPage;

    /** @var string */
    private $sTitle;

    /** @var string */
    private $sCurrentDate;

    /** @var int */
    private $iTotalBirths;

    public function __construct()
    {
        parent::__construct();

        $this->oBirthModel = new BirthdayModel;
        $this->oPage = new Page;

        $this->sCurrentDate = $this->dateTime->get()->date();
        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class

        /**
         *  Predefined meta_description.
         */
        $this->view->meta_description = t("Users' Birthdays %0%", $this->sCurrentDate);

        /**
         *  Predefined meta_keywords tags.
         */
        $this->view->meta_keywords = t('birthday,birthdate,anniversary,birth,friend,dating,social networking,profile,social');
    }

    /**
     * @param string $sGender
     *
     * @return void
     */
    public function index($sGender = BirthdayModel::ALL)
    {
        $this->checkType($sGender);

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalBirths, self::MAX_PROFILE_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->iTotalBirths = $this->oBirthModel->get(
            $sGender,
            true,
            SearchCoreModel::LAST_ACTIVITY,
            SearchCoreModel::DESC,
            null,
            null
        );
        $oBirths = $this->oBirthModel->get(
            $sGender,
            false,
            SearchCoreModel::LAST_ACTIVITY,
            SearchCoreModel::DESC,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $sHtmlCurrentDate = ' &ndash; <span class="pH3">' . $this->sCurrentDate . '</span>';
        $this->sTitle = nt('%n% Birthday', '%n% Birthdays', $this->iTotalBirths) . $sHtmlCurrentDate;
        $this->view->page_title = $this->view->h1_title = $this->sTitle;

        if ($sGender !== BirthdayModel::ALL) {
            $this->view->h3_title = '<span class="pH0">' . t($sGender) . '</span>';
        }

        $this->view->births = $oBirths;

        $this->output();
    }

    /**
     * @param string $sSexType
     *
     * @return string|void
     */
    private function checkType($sSexType)
    {
        switch ($sSexType) {
            case BirthdayModel::ALL:
            case BirthdayModel::COUPLE:
            case BirthdayModel::MALE:
            case BirthdayModel::FEMALE:
                return $sSexType;

            default:
                $this->displayPageNotFound();
        }
    }
}
