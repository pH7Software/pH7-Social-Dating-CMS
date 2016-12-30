<?php
/**
 * @title            Analytics Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

class Analytics
{

    /**
     * Update the analytics API code.
     *
     * @param string $sCode
     * @return mixed (integer | boolean) Returns the number of rows on success or FALSE on failure.
     */
    public function updateApi($sCode)
    {
        return Engine\Record::getInstance()->update('AnalyticsApi', 'code', $sCode);
    }

}
