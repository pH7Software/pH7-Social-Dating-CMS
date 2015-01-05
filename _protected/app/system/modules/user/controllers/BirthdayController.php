<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

use PH7\Framework\Navigation\Page;

class BirthdayController extends Controller
{

    private $oBirthModel, $oPage, $sTitle, $sCurrentDate, $iTotalBirths;

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
        $this->view->meta_description = t('Users Birthday %0%', $this->sCurrentDate);

        /**
         *  Predefined meta_keywords tags.
         */
        $this->view->meta_keywords = t('birthday,birthdate,anniversary,birth,friend,dating,social networking,profile,social');
    }

    public function index($sGender = BirthdayModel::ALL)
    {
        $this->checkType($sGender);

        $this->view->total_pages = $this->oPage->getTotalPages($this->iTotalBirths, 20);
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->iTotalBirths = $this->oBirthModel->get($sGender, true, SearchCoreModel::LAST_ACTIVITY, SearchCoreModel::DESC, null, null);
        $oBirths = $this->oBirthModel->get($sGender, false, SearchCoreModel::LAST_ACTIVITY, SearchCoreModel::DESC, $this->oPage->getFirstItem(), $this->oPage->getNbItemsByPage());

        $this->sTitle = t('Users Birthday (<span class="pH3">%0%</span>)', $this->sCurrentDate);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        if ($sGender != BirthdayModel::ALL) $this->view->h3_title = '<span class="pH0">' . t($sGender) . '</span>';
        $this->view->total_births = $this->iTotalBirths;
        $this->view->births = $oBirths;

        $this->output();
    }

    protected function checkType($sSexType)
    {
        switch ($sSexType)
        {
            case BirthdayModel::ALL:
            case BirthdayModel::COUPLE:
            case BirthdayModel::MALE:
            case BirthdayModel::FEMALE:
                return $sSexType;
            break;

            default:
                $this->displayPageNotFound();
        }
    }

}
