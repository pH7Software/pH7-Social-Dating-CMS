<?php
/**
 * @title          Set User Last Activity
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax
 * @version        1.0
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Session\Session;

// Only for members
if (UserCore::auth()) {
    (new UserCoreModel)->setLastActivity((new Session)->get('member_id'));
}
