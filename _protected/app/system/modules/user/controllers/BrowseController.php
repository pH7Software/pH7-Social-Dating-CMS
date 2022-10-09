<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Url\Header;

class BrowseController extends Controller
{
    private const MAX_PROFILES_PER_PAGE = 52;

    private UserModel $oUserModel;
    private Page $oPage;
    private int $iTotalUsers;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserModel;
        $this->oPage = new Page;
    }

    public function index(): void
    {
        $this->iTotalUsers = $this->oUserModel->search($_GET, true, null, null);
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalUsers,
            self::MAX_PROFILES_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $aUsers = $this->oUserModel->search(
            $_GET,
            false,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if ($this->isSearch() && !empty($aUsers)) {
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
            $this->view->users = $aUsers;
            $this->output();
        }
    }

    private function isSearch(): bool
    {
        return !empty($_GET) && count($_GET) > 1;
    }
}
