<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Module\Various as SysMod;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AccountController extends Controller
{
    public function index()
    {
        Header::redirect($this->getHomepageUrl());
    }

    /**
     * @param string $sMail
     * @param string $sHash
     *
     * @return void
     */
    public function activate($sMail, $sHash)
    {
        (new UserCore)->activateAccount(
            $sMail,
            $sHash,
            $this->config,
            $this->registry
        );
    }

    /**
     * Redirect this page to the user homepage.
     *
     * @return string
     */
    private function getHomepageUrl()
    {
        if (SysMod::isEnabled('user-dashboard')) {
            return Uri::get('user-dashboard', 'main', 'index');
        }

        return Uri::get('user', 'main', 'index');
    }
}
