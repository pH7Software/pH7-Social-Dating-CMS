<?php
/**
 * @title            Cross-site scripting Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security / Validate
 */

namespace PH7\Framework\Security\Validate;

defined('PH7') or exit('Restricted access');

abstract class Xss
{
    /**
     * Purify an array of any dimension.
     *
     * @param array $aValues Values to purify.
     *
     * @return array Values purified.
     */
    protected function arrayClean(array $aValues)
    {
        foreach ($aValues as $sKey => $mVal) {
            $aValues[$sKey] = is_array($mVal) ? $this->arrayClean($mVal) : $this->clean($mVal);
        }

        return $aValues;
    }

    /**
     * XSS Clean.
     *
     * @param string $sValue Value to purify.
     *
     * @return string Value purified.
     */
    protected function clean($sValue)
    {

    }
}
