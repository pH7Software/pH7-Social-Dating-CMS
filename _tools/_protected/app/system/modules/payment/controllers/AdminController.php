<?php
/**
 * @title          Admin Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 */

namespace PH7;

use PDOException;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminController extends MainController
{
    public function index()
    {
        $this->sTitle = t('Administration of Payment System');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function config()
    {
        $this->sTitle = t('Configure Payment Gateways');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function membershipList()
    {
        $oMembership = $this->oPayModel->getMemberships();

        if (empty($oMembership)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            $this->sTitle = t('Memberships List');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->memberships = $oMembership;
            $this->output();
        }
    }

    public function addMembership()
    {
        $this->sTitle = t('Add Membership');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function editMembership()
    {
        $this->sTitle = t('Update Membership');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function deleteMembership()
    {
        $iMembershipId = $this->httpRequest->post('id', 'int');

        if (GroupId::undeletable($iMembershipId)) {
            echo t('You cannot delete the default membership group.');
            exit;
        }

        $bHasError = false;
        $sMsg = t('The Membership has been removed!');

        try {
            $this->oPayModel->deleteMembership($iMembershipId);
        } catch (PDOException $oE) {
            $bHasError = true;
            $sMsg = t('This one cannot be deleted.');
        }

        $this->clearCache();
        Header::redirect(
            Uri::get('payment', 'admin', 'membershiplist'),
            $sMsg,
            ($bHasError ? Design::ERROR_TYPE : Design::SUCCESS_TYPE)
        );
    }

    /**
     * Clean UserCoreModel cache.
     *
     * @return void
     */
    private function clearCache()
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
