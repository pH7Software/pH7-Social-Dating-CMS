<?php
/**
 * @title            Analytics Model Class.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;

use PH7\DbTableName;

defined('PH7') or exit('Restricted access');

class Analytics
{
    /**
     * Update the analytics API code.
     *
     * @param string $sCode
     *
     * @return int|bool Returns the number of rows on success or FALSE on failure.
     */
    public function updateApi($sCode)
    {
        return Engine\Record::getInstance()->update(
            DbTableName::ANALYTIC_API,
            'code',
            $sCode
        );
    }
}
