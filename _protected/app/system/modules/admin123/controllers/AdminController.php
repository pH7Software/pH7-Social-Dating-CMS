<?php
/**
 * @title          Admin Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    use BulkAction;

    const PROFILES_PER_PAGE = 15;

    /** @var AdminModel */
    private $oAdminModel;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iCurrentAdminId;

    /** @var int */
    private $iTotalAdmins;

    public function __construct()
    {
        parent::__construct();

        $this->oAdminModel = new AdminModel;
        $this->iCurrentAdminId = (int)$this->session->get('admin_id');
    }

    public function index()
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'admin', 'browse')
        );
    }

    public function browse()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalAdmins = $this->oAdminModel->searchAdmin(
            $sKeywords,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages(
            $this->iTotalAdmins,
            self::PROFILES_PER_PAGE
        );
        $this->view->current_page = $oPage->getCurrentPage();
        $oSearch = $this->oAdminModel->searchAdmin(
            $sKeywords,
            false,
            $sOrder,
            $iSort,
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage()
        );
        unset($oPage);

        if (empty($oSearch)) {
            $this->design->setRedirect(
                Uri::get(PH7_ADMIN_MOD, 'admin', 'browse')
            );

            $this->displayPageNotFound(t('Sorry, Your search returned no results!'));
        } else {
            // Adding the JS form file
            $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

            // Assigns variables for views
            $this->view->designSecurity = new HtmlSecurity; // Security Design Class
            $this->view->dateTime = $this->dateTime; // Date Time Class

            $this->sTitle = t('Browse Admins');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Admin', '%n% Admins', $this->iTotalAdmins);
            $this->view->current_admin_id = $this->iCurrentAdminId;
            $this->view->browse = $oSearch;
        }

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Admin Search');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add an Admin');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function delete()
    {
        try {
            $aData = explode('_', $this->httpRequest->post('id'));
            $iId = (int)$aData[0];
            $sUsername = (string)$aData[1];

            if ($iId === $this->iCurrentAdminId) {
                $sMsgType = Design::ERROR_TYPE;
                $sMsg = t('Oops! You cannot remove your own admin profile.');
            } else {
                (new Admin)->delete($iId, $sUsername, $this->oAdminModel);
                $sMsgType = Design::SUCCESS_TYPE;
                $sMsg = t('The admin has been deleted.');
            }

            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'admin', 'browse'),
                $sMsg,
                $sMsgType
            );
        } catch (ForbiddenActionException $oExcept) {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'admin', 'browse'),
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }

    public function deleteAll()
    {
        // Default redirect message state
        $sMsgType = Design::SUCCESS_TYPE;
        $sMsg = t('The admin(s) has/have been deleted.');

        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        try {
            if (!(new SecurityToken)->check('admin_action')) {
                $sMsg = Form::errorTokenMsg();
            } elseif ($bActionsEligible) {
                foreach ($aActions as $sAction) {
                    $aData = explode('_', $sAction);
                    $iId = (int)$aData[0];
                    $sUsername = (string)$aData[1];

                    if ($iId === $this->iCurrentAdminId) {
                        $sMsgType = Design::ERROR_TYPE;
                        $sMsg = t('Oops! You cannot remove your own admin profile.');
                    } else {
                        (new Admin)->delete($iId, $sUsername, $this->oAdminModel);
                    }
                }
            }

            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'admin', 'browse'),
                $sMsg,
                $sMsgType
            );
        } catch (ForbiddenActionException $oExcept) {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'admin', 'browse'),
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }
}
