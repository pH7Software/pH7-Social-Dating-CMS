<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Config
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if ($this->isUserNotAllowed()) {
            $this->signUpRedirect();
        }

        if ($this->registry->controller === 'AdminController' && !AdminCore::auth()) {
            // For security reasons, don't redirect user to admin panel URL
            Header::redirect(
                Uri::get('user', 'main', 'login'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * @return bool
     */
    private function isUserNotAllowed()
    {
        $this->registry->controller === 'MainController' &&
            !$this->session->exists(SmsVerificationCore::PROFILE_ID_SESS_NAME);
    }
}
