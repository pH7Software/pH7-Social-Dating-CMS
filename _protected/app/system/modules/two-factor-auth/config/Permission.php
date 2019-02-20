<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if ($this->registry->action === 'verificationcode' &&
            !$this->session->exists(TwoFactorAuthCore::PROFILE_ID_SESS_NAME)
        ) {
            Header::redirect($this->registry->site_url);
        }

        if ($this->registry->action === 'setup' &&
            !UserCore::auth() && !AffiliateCore::auth() && !AdminCore::auth()
        ) {
            Header::redirect($this->registry->site_url);
        }
    }
}
