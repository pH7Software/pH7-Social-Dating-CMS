<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Config
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use Teapot\StatusCode;

defined('PH7') or die('Restricted access');

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
