<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!AdminCore::auth()) {
            // For security reasons, we don't redirect the user to the admin panel URL
            Header::redirect(
                PH7_URL_ROOT,
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
