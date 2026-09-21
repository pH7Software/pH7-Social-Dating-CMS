<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if ($this->registry->action === 'verificationcode') {
            $mModule = $this->httpRequest->get('mod');
            $sModule = is_string($mModule) ? $mModule : '';
            if (TwoFactorAuthCore::getChallengeProfileId($this->session, $sModule) === null) {
                TwoFactorAuthCore::clearChallenge($this->session);
                Header::redirect(
                    TwoFactorAuthCore::getLoginUrl($sModule),
                    t('Please sign in again. Your verification session is invalid or has expired.'),
                    Design::ERROR_TYPE
                );
            }
        }

        if ($this->registry->action === 'setup'
            && !UserCore::auth() && !AffiliateCore::auth() && !AdminCore::auth()
        ) {
            Header::redirect($this->registry->site_url);
        }
    }
}
