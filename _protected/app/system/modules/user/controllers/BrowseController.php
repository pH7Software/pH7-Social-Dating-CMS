<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
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

    public function index()
    {
        $this->iTotalUsers = $this->oUserModel->search($_GET, true, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalUsers,
            self::MAX_PROFILE_PER_PAGE
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
                Uri::get('user', 'browse', 'index'),
                t('No results. Please try again with wider or different search criteria.'),
                Design::ERROR_TYPE
            );
        } else {
            /**
             * @internal Here, we can put HTML tags in `<title>` tag since the template will strip them out before the output.
             */
            $this->view->page_title = t('Browse Members');
            $this->view->h1_title = '<span class="pH1">' . t('Browse Members') . '</span>';
            $this->view->h3_title = t('Meet new People with %0%', '<span class="pH0">' . $this->registry->site_name . '</span>');
            $this->view->meta_description = t('Meet new People and Friends near you with %site_name% - Browse Members');
            $this->view->avatarDesign = new AvatarDesignCore;
            $this->view->users = $oUsers;
            $this->output();
        }
    }
}
