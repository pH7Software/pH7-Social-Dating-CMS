<?php
/**
 * @desc             Loading template files.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2010-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout
 */

declare(strict_types=1);

namespace PH7\Framework\Layout;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Registry\Registry;

class LoadTemplate
{
    private const COOKIE_NAME = 'site_tpl';
    private const COOKIE_LIFETIME = 172800;
    private const REQUEST_PARAM_NAME = 'tpl';
    private const MAX_TPL_FOLDER_LENGTH = 50;


    private Config $oConfig;

    private string $sDefaultTpl;

    private ?string $sUserTpl;

    private string $sTplName;

    private string $sModTplName;

    private string $sMailTplName;

    public function __construct()
    {
        $this->oConfig = Config::getInstance();

        $this->initializeUserTplOverride();
    }

    /**
     * Set the default template name.
     *
     * @param string $sNewDefTpl Template name.
     *
     * @return self
     */
    public function setDefaultTpl(string $sNewDefTpl)
    {
        $this->sDefaultTpl = $sNewDefTpl;

        return $this;
    }

    /**
     * Get the current template name.
     *
     * @return string The template name.
     */
    public function getTpl(): string
    {
        return $this->sTplName;
    }

    /**
     * Get the current module template name.
     *
     * @return string The template name.
     */
    public function getModTpl(): string
    {
        return $this->sModTplName;
    }

    /**
     * Get the current mail template name.
     *
     * @return string The mail template name.
     */
    public function getMailTpl(): string
    {
        return $this->sMailTplName;
    }

    /**
     * @return self
     *
     * @throws Exception If the template file is not found.
     */
    public function tpl()
    {
        if ($this->doesUserTplExist()) {
            $this->sTplName = $this->sUserTpl;
        } elseif ($this->doesDefaultSettingTplExist()) {
            $this->sTplName = $this->sDefaultTpl;
        } elseif ($this->doesSystemTplExist()) {
            $this->sTplName = PH7_DEFAULT_THEME;
        } else {
            throw new Exception(
                sprintf(
                    "Template file not found! File: %s doesn't exist.",
                    PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE
                )
            );
        }

        return $this;
    }

    /**
     * @return self
     *
     * @throws Exception If the module template file is not found.
     */
    public function modTpl()
    {
        $oRegistry = Registry::getInstance();

        if ($this->oConfig->load($oRegistry->path_module_views . $this->sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sModTplName = $this->sUserTpl;
        } elseif ($this->oConfig->load($oRegistry->path_module_views . $this->sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sModTplName = $this->sDefaultTpl;
        } elseif ($this->oConfig->load($oRegistry->path_module_views . PH7_DEFAULT_TPL_MOD . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sModTplName = PH7_DEFAULT_TPL_MOD;
        } else {
            throw new Exception(
                sprintf(
                    "Module template file not found! File: %s doesn't exist.",
                    $oRegistry->path_module_views . PH7_DEFAULT_TPL_MOD . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE
                )
            );
        }

        unset($oRegistry);

        return $this;
    }

    /**
     * @return self
     *
     * @throws Exception If the mail template file is not found.
     */
    public function mailTpl()
    {
        if ($this->oConfig->load(PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . $this->sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sMailTplName = $this->sUserTpl;
        } elseif ($this->oConfig->load(PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . $this->sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sMailTplName = $this->sDefaultTpl;
        } elseif ($this->oConfig->load(PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sMailTplName = PH7_DEFAULT_THEME;
        } else {
            throw new Exception(
                sprintf(
                    "Mail template file not found! File: %s doesn't exist.",
                    PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE
                )
            );
        }

        return $this;
    }

    private function initializeUserTplOverride()
    {
        $oCookie = new Cookie;

        $this->sUserTpl = null;
        if ($this->isTplParamSet()) {
            $this->sUserTpl = $_REQUEST[self::REQUEST_PARAM_NAME];
            $oCookie->set(self::COOKIE_NAME, $this->sUserTpl, static::COOKIE_LIFETIME);
        } elseif ($oCookie->exists(self::COOKIE_NAME)) {
            $this->sUserTpl = $oCookie->get(self::COOKIE_NAME);
        }

        unset($oCookie);
    }

    /**
     * @return bool
     */
    private function doesUserTplExist()
    {
        return !empty($this->sUserTpl) &&
            $this->oConfig->load(PH7_PATH_TPL . $this->sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
    }

    /**
     * @return bool
     */
    private function doesDefaultSettingTplExist()
    {
        return $this->oConfig->load(PH7_PATH_TPL . $this->sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
    }

    private function doesSystemTplExist(): bool
    {
        return $this->oConfig->load(PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
    }

    /**
     * Check if a template name has been specified and if it doesn't exceed the maximum length (50 characters).
     */
    private function isTplParamSet(): bool
    {
        return !empty($_REQUEST[self::REQUEST_PARAM_NAME]) &&
            strlen($_REQUEST[self::REQUEST_PARAM_NAME]) <= static::MAX_TPL_FOLDER_LENGTH;
    }
}
