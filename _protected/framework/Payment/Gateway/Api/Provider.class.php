<?php
/**
 * @title            Provider Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

abstract class Provider
{
    protected $aParams = [];

    /**
     * @param string $sName
     * @param string $sValue
     *
     * @return self
     */
    public function param($sName, $sValue)
    {
        $this->aParams[$sName] = $sValue;

        return $this;
    }

    /**
     * Generate Output.
     *
     * @return string HTML tags.
     */
    public function generate()
    {
        $sHtml = ''; // Default Value

        foreach ($this->aParams as $sKey => $sVal) {
            $sHtml .= "<input type=\"hidden\" name=\"$sKey\" value=\"$sVal\" />\n";
        }

        return $sHtml;
    }
}
