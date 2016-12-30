<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2014-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model
 */

namespace PH7\Framework\Mvc\Model;
defined('PH7') or exit('Restricted access');

class License extends Engine\Model
{

    /**
     * Get the license key.
     *
     * @param integer $iId The License ID. Default 1
     * @return string
     */
    public function get($iId = 1)
    {
        return $this->orm->getOne('License', 'licenseId', $iId, 'licenseKey')->licenseKey;
    }

    /**
     * Update the license key.
     *
     * @param string $sKey
     * @param integer $iId Column ID. Default 1
     * @return mixed (integer | boolean) Returns the number of rows on success or FALSE on failure.
     */
    public function save($sKey, $iId = 1)
    {
        return $this->orm->update('License', 'licenseKey', $sKey, 'licenseId', $iId);
    }

}
