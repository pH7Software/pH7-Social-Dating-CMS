<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Config
 */

namespace PH7;

use PH7\Framework\Url\Header;

defined('PH7') or die('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        /**
         * Make the the page is requested directly from the signup process by a user.
         */
        if (!$this->session->exists('mail_step3')) {
            Header::redirect(PH7_URL_ROOT);
        }
    }
}
