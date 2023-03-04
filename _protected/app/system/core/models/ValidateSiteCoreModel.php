<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;

class ValidateSiteCoreModel extends Framework\Mvc\Model\Engine\Model
{
    /**
     * Check if the site has been validated or not.
     *
     * @internal This method will be used also in the "admin" module through the "ValidateSiteCore" class,
     * so it needs to be in a Core model and not in the local "ph7cms-helper" module.
     *
     * @return bool
     */
    public function is()
    {
        return (bool)DbConfig::getSetting('isSiteValidated');
    }
}
