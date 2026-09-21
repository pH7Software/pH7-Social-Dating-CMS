<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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
            SmsVerificationCore::clearChallenge($this->session);
            Header::redirect(
                Uri::get('user', 'main', 'login'),
                t('Please sign in again. Your phone verification session has expired.'),
                Design::ERROR_TYPE
            );
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
        return $this->registry->controller === 'MainController'
            && SmsVerificationCore::getChallengeProfileId($this->session) === null;
    }
}
