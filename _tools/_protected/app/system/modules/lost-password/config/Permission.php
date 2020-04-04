<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if ((UserCore::auth() || AffiliateCore::auth() || AdminCore::auth()) &&
            ($this->registry->action === 'forgot' || $this->registry->action === 'reset')
        ) {
            Header::redirect(
                Uri::get('lost-password', 'main', 'account'),
                $this->alreadyConnectedMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
