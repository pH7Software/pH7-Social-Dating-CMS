<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Birthday / Controller
 */

namespace PH7;

use PH7\Framework\Navigation\Page;

class UserController extends Controller
{
    private const MAX_PROFILES_PER_PAGE = 20;

    private BirthdayModel $oBirthModel;
    private Page $oPage;
    private string $sCurrentDate;
    private int $iTotalBirths = 0;

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
        $this->view->meta_keywords = t(
            'birthday,birthdate,anniversary,birth,friend,dating,social networking,profile,social'
        );
    }

    public function index(string $sGender = BirthdayModel::ALL): void
    {
        $this->checkType($sGender);

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalBirths,
            self::MAX_PROFILES_PER_PAGE
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

        $this->view->page_title = $this->view->h1_title = $this->getPageTitle();

        if ($sGender !== BirthdayModel::ALL) {
            $this->view->h3_title = '<span class="pH0">' . t($sGender) . '</span>';
        }

        $this->view->births = $oBirths;

        $this->output();
    }

    private function getPageTitle(): string
    {
        $sHtmlCurrentDate = ' &ndash; <span class="pH3">' . $this->sCurrentDate . '</span>';
        return nt('%n% Birthday', '%n% Birthdays', $this->iTotalBirths) . $sHtmlCurrentDate;
    }

    /**
     * @return string|never
     * TODO With PHP 8.1, add union types "string|never" since `displayPageNotFound` terminates with exit()
     */
    private function checkType(string $sSexType)
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
