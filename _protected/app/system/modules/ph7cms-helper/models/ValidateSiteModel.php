<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / pH7CMS Helper / Model
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;

class ValidateSiteModel extends ValidateSiteCoreModel
{
    /**
     * Set a site validated/unvalidated.
     *
     * @param int $iStatus Set "1" to validate the site or "0" to unvalidated it. Default: 1
     *
     * @return int 1 on success.
     */
    public function set(int $iStatus = 1)
    {
        return DbConfig::setSetting($iStatus, 'isSiteValidated');
    }
}
