<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Config
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\JustHttp\StatusCode;

defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Make sure the page is only requested through the signup process.
         */
        if (!$this->session->exists('mail_step3')) {
            Header::redirect(
                Uri::get(
                    'error',
                    'http',
                    'index',
                    StatusCode::FORBIDDEN
                )
            );
        }
    }
}
