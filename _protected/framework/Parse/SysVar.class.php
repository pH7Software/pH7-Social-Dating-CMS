<?php
/**
 * @title            SysVar Class
 * @desc             Parse the global pH7CMS variables.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;

class SysVar
{
    const REGEX_NOT_PARSING = '/#!.+!#/';
    const NOT_PARSING_DELIMITERS = ['#!', '!#'];

    /** @var string */
    private $sVar;

    /** @var array */
    private static $aKernelVariables = [
        '%software_name%' => Kernel::SOFTWARE_NAME,
        '%software_author%' => 'Pierre-Henry Soria',
        '%software_version_name%' => Kernel::SOFTWARE_VERSION_NAME,
        '%software_version%' => Kernel::SOFTWARE_VERSION,
        '%software_build%' => Kernel::SOFTWARE_BUILD,
        '%software_email%' => Kernel::SOFTWARE_EMAIL,
        '%software_website%' => Kernel::SOFTWARE_WEBSITE
    ];

    /**
     * Parser for the System variables.
     *
     * @param string $sVar
     *
     * @return string The new parsed text
     */
    public function parse($sVar)
    {
        $this->sVar = $sVar;

        if ($this->notParsingVars()) {
            $this->removeNotParsingDelimiters();
            return $this->sVar;
        }

        $this->parseSiteVars();
        $this->parseAffiliateVars();
        $this->parseGlobalVars();
        $this->parseKernelVars();

        // Output
        return $this->sVar;
    }

    private function parseSiteVars()
    {
        $oRegistry = Registry::getInstance();
        $this->sVar = str_replace('%site_name%', $oRegistry->site_name, $this->sVar);
        $this->sVar = str_replace('%url_relative%', PH7_RELATIVE, $this->sVar);
        $this->sVar = str_replace(['%site_url%', '%url_root%'], $oRegistry->site_url, $this->sVar);
        $this->sVar = str_replace('%url_static%', PH7_URL_STATIC, $this->sVar);
        unset($oRegistry);
    }

    private function parseAffiliateVars()
    {
        $oSession = new Session;
        $sAffUsername = $oSession->exists('affiliate_username') ? $oSession->get('affiliate_username') : 'aid';
        $this->sVar = str_replace(
            '%affiliate_url%',
            Uri::get('affiliate', 'router', 'refer', $sAffUsername),
            $this->sVar
        );
        unset($oSession);
    }

    private function parseGlobalVars()
    {
        $this->sVar = str_replace('%ip%', Ip::get(), $this->sVar);
    }

    private function parseKernelVars()
    {
        foreach (self::$aKernelVariables as $sKey => $sValue) {
            $this->sVar = str_replace($sKey, $sValue, $this->sVar);
        }
    }

    private function removeNotParsingDelimiters()
    {
        $this->sVar = str_replace(self::NOT_PARSING_DELIMITERS, '', $this->sVar);
    }

    /**
     * @return bool
     */
    private function notParsingVars()
    {
        return preg_match(self::REGEX_NOT_PARSING, $this->sVar);
    }
}
