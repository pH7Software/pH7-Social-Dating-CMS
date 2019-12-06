<?php
/**
 * @title          Account Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AccountController extends Controller
{
    /** @var string */
    private $sTitle;

    public function index()
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'account', 'edit')
        );
    }

    public function password()
    {
        $this->sTitle = t('Change Password');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit your account');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->is_edit_mode = $this->httpRequest->getExists('profile_id');

        $this->output();
    }
}
