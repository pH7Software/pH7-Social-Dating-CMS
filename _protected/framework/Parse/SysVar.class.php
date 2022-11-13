<?php
/**
 * @desc             Parse the global pH7Builder variables.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Parse
 */

declare(strict_types=1);

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;

class SysVar
{
    private const REGEX_NOT_PARSING = '/#!.+!#/';
    private const NOT_PARSING_DELIMITERS = ['#!', '!#'];

    private Str $oStr;

    private string $sVar;

    private static array $aKernelVariables = [
        '%software_name%' => Kernel::SOFTWARE_NAME,
        '%software_author%' => Kernel::SOFTWARE_AUTHOR,
        '%software_version_name%' => Kernel::SOFTWARE_VERSION_NAME,
        '%software_version%' => Kernel::SOFTWARE_VERSION,
        '%software_build%' => Kernel::SOFTWARE_BUILD,
        '%software_email%' => Kernel::SOFTWARE_EMAIL,
        '%software_website%' => Kernel::SOFTWARE_WEBSITE
    ];

    public function __construct()
    {
        $this->oStr = new Str;
    }

    /**
     * Parser for the System variables.
     */
    public function parse(string $sVar): string
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

    private function parseSiteVars(): void
    {
        $oRegistry = Registry::getInstance();
        $this->sVar = $this->oStr->replace('%site_name%', (string)$oRegistry->site_name, $this->sVar);
        $this->sVar = $this->oStr->replace('%url_relative%', PH7_RELATIVE, $this->sVar);
        $this->sVar = $this->oStr->replace(['%site_url%', '%url_root%'], (string)$oRegistry->site_url, $this->sVar);
        $this->sVar = $this->oStr->replace('%url_static%', PH7_URL_STATIC, $this->sVar);
        unset($oRegistry);
    }

    private function parseAffiliateVars(): void
    {
        $oSession = new Session;
        $sAffUsername = $oSession->exists('affiliate_username') ? $oSession->get('affiliate_username') : 'aid';
        $this->sVar = $this->oStr->replace(
            '%affiliate_url%',
            Uri::get('affiliate', 'router', 'refer', $sAffUsername),
            $this->sVar
        );
        unset($oSession);
    }

    private function parseGlobalVars(): void
    {
        $this->sVar = $this->oStr->replace('%ip%', Ip::get(), $this->sVar);
    }

    private function parseKernelVars(): void
    {
        foreach (self::$aKernelVariables as $sKey => $sValue) {
            $this->sVar = $this->oStr->replace($sKey, $sValue, $this->sVar);
        }
    }

    private function removeNotParsingDelimiters(): void
    {
        $this->sVar = $this->oStr->replace(self::NOT_PARSING_DELIMITERS, '', $this->sVar);
    }

    private function notParsingVars(): bool
    {
        return (bool)preg_match(self::REGEX_NOT_PARSING, $this->sVar);
    }
}
