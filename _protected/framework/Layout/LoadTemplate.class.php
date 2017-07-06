<?php
/**
 * @title            Load Template Class
 * @desc             Loading template files.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2010-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout
 */

namespace PH7\Framework\Layout;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Registry\Registry;

class LoadTemplate
{
    /** @var Config */
    private $oConfig;

    /** @var string */
    private $sDefaultTpl;

    /** @var string */
    private $sUserTpl;

    /** @var string */
    private $sTplName;

    /** @var string */
    private $sModTplName;

    /** @var string */
    private $sMailTplName;

    public function __construct()
    {
        $this->oConfig = Config::getInstance();
        $oCookie = new Cookie;

        // Check a template name has been entered and if it exceeds the maximum length (49 characters).
        if (!empty($_REQUEST['tpl']) && strlen($_REQUEST['tpl']) < 50) {
            $this->sUserTpl = $_REQUEST['tpl'];
            $oCookie->set('site_tpl', $this->sUserTpl, 60 * 60 * 48);
        } elseif ($oCookie->exists('site_tpl')) {
            $this->sUserTpl = $oCookie->get('site_tpl');
        }

        unset($oCookie);
    }

    /**
     * Set the default template name.
     *
     * @param string $sNewDefTpl Template name.
     *
     * @return self
     */
    public function setDefaultTpl($sNewDefTpl)
    {
        $this->sDefaultTpl = $sNewDefTpl;

        return $this;
    }

    /**
     * Get the current template name.
     *
     * @return string The template name.
     */
    public function getTpl()
    {
        return $this->sTplName;
    }

    /**
     * Get the current module template name.
     *
     * @return string The template name.
     */
    public function getModTpl()
    {
        return $this->sModTplName;
    }

    /**
     * Get the current mail template name.
     *
     * @return string The mail template name.
     */
    public function getMailTpl()
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
        if (
            !empty($this->sUserTpl) &&
            $this->oConfig->load(PH7_PATH_TPL . $this->sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)
        ) {
            $this->sTplName = $this->sUserTpl;
        } elseif ($this->oConfig->load(PH7_PATH_TPL . $this->sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sTplName = $this->sDefaultTpl;
        } elseif ($this->oConfig->load(PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->sTplName = PH7_DEFAULT_THEME;
        } else {
            throw new Exception('Template file not found! File: \'' . PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' doesn\'t exist.');
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
            throw new Exception('Module template file not found! File: \'' . $oRegistry->path_module_views . PH7_DEFAULT_TPL_MOD . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' doesn\'t exist.');
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
            throw new Exception('Mail template file not found! File: \'' . PH7_PATH_SYS . 'global' . PH7_DS . PH7_VIEWS . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' doesn\'t exist.');
        }

        return $this;
    }
}
