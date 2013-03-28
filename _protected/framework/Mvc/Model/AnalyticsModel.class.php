<?php
/**
 * @title            Analytics Model Class.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

class AnalyticsModel
{

    /**
     * Update the analytics API code.
     *
     * @param string $sCode
     * @return integer Number of rows.
     */
    public function updateApi($sCode)
    {
        return Engine\Record::getInstance()->update('AnalyticsApi', 'code', $sCode);
    }

}
