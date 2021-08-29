<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2014-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Chatroulette / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if ($this->isNotAdmin()) {
            if (!$this->checkMembership() || !$this->group->chatroulette) {
                $this->paymentRedirect();
            }
        }
    }
}
