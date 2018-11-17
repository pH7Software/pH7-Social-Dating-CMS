<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Url\Header;

class BrowseController extends Controller
{
    const MAX_PROFILE_PER_PAGE = 40;

    /** @var UserModel */
    private $oUserModel;

    /** @var Page */
    private $oPage;

    /** @var int */
    private $iTotalUsers;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserModel;
        $this->oPage = new Page;
    }

    public function seller()
    {
        $this->iTotalUsers = $this->oUserModel->search($_GET, true, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalUsers, self::MAX_PROFILE_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oUsers = $this->oUserModel->search(
            $_GET,
            false,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oUsers)) {
            Header::redirect(
                Uri::get('realestate', 'search', 'seller'),
                t('No results. Please try again with wider or new search criteria.'),
                Design::WARNING_TYPE
            );
        } else {
            // We can put HTML tags in the <title> tag as our template engine will remove all HTML tags present in the title tag, ...
            $this->view->page_title = t('Browse Sellers');
            $this->view->h1_title = '<span class="pH1">' . t('Browse Sellers') . '</span>';
            $this->view->h3_title = t('Find Sellers with %0%', '<span class="pH0">' . $this->registry->site_name . '</span>');
            $this->view->meta_description = t('Find the best properties with %site_name% - Browse Properties');
            $this->view->avatarDesign = new AvatarDesignCore;
            $this->view->users = $oUsers;

            UserSpyCoreModel::addUserAction(
                $this->session->get('member_id'),
                Uri::get('realestate', 'browse', 'seller'),
                t('%0% is searching for sellers.', $this->session->get('member_username'))
            );

            $this->output();
        }
    }

    public function buyer()
    {
        $this->iTotalUsers = $this->oUserModel->search($_GET, true, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalUsers, self::MAX_PROFILE_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oUsers = $this->oUserModel->search(
            $_GET,
            false,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oUsers)) {
            Header::redirect(
                Uri::get('realestate', 'search', 'buyer'),
                t('No results. Please try again with wider or new search criteria.'),
                Design::WARNING_TYPE
            );
        } else {
            // We can put HTML tags in the <title> tag as our template engine will remove all HTML tags present in the title tag, ...
            $this->view->page_title = t('Browse Buyers');
            $this->view->h1_title = '<span class="pH1">' . t('Browse Buyers') . '</span>';
            $this->view->h3_title = t('Find Buyers with %0%', '<span class="pH0">' . $this->registry->site_name . '</span>');
            $this->view->meta_description = t('Find Buyers near you with %site_name% - Browse Buyers');
            $this->view->avatarDesign = new AvatarDesignCore;
            $this->view->users = $oUsers;

            UserSpyCoreModel::addUserAction(
                $this->session->get('member_id'),
                Uri::get('realestate', 'browse', 'buyer'),
                t('%0% is searching for buyers.', $this->session->get('member_username'))
            );

            $this->output();
        }
    }
}
