<?php

/**
 * @title            HTML Purifier Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7\Framework\Security\Validate;

defined('PH7') or exit('Restricted access');

class Purifier extends Xss
{
    private static ?\HTMLPurifier $oSharedPurifier = null;

    private \HTMLPurifier $oPurifier;

    public function __construct()
    {
        if (self::$oSharedPurifier === null) {
            $oConfig = \HTMLPurifier_Config::createDefault();
            $oConfig->set('Core.Encoding', PH7_ENCODING);
            $oConfig->set('Cache.DefinitionImpl', null);
            $oConfig->set('Attr.EnableID', false);
            $oConfig->set('HTML.ForbiddenAttributes', ['style']);
            $oConfig->set('HTML.SafeIframe', false);
            $oConfig->set(
                'URI.AllowedSchemes',
                [
                    'http' => true,
                    'https' => true,
                    'mailto' => true
                ]
            );

            self::$oSharedPurifier = new \HTMLPurifier($oConfig);
        }

        $this->oPurifier = self::$oSharedPurifier;
    }

    /**
     * Clean a string against XSS vulnerabilities.
     *
     * @param string|array $mStr value to clean
     *
     * @return string|array value cleaned
     */
    public function xssClean($mStr)
    {
        return is_array($mStr) ? $this->arrayClean($mStr) : $this->clean($mStr);
    }

    protected function clean($sValue)
    {
        return $this->oPurifier->purify((string)$sValue);
    }
}
