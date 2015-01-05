<?php
/**
 * @title            Load Template Class
 * @desc             Loading template files.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout
 * @version          1.0
 */

namespace PH7\Framework\Layout;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config, PH7\Framework\Cookie\Cookie;

class LoadTemplate
{

    private $_oConfig, $_sDefaultTpl, $_sUserTpl, $_sTplName, $_sModTplName;

    public function __construct()
    {
        $this->_oConfig = Config::getInstance();
        $oCookie = new Cookie;

        // Check a template name has been entered and if it exceeds the maximum length (49 characters).
        if (!empty($_REQUEST['tpl']) && strlen($_REQUEST['tpl']) < 50)
        {
            $this->_sUserTpl = $_REQUEST['tpl'];
            $oCookie->set('site_tpl', $this->_sUserTpl, 60 * 60 * 48);
        }
        else if ($oCookie->exists('site_tpl'))
        {
            $this->_sUserTpl = $oCookie->get('site_tpl');
        }

        unset($oCookie);
    }

    /**
     * Set the default template name.
     *
     * @param string $sNewDefTpl Template name.
     * @return object $this
     */
    public function setDefaultTpl($sNewDefTpl)
    {
        $this->_sDefaultTpl = $sNewDefTpl;

        return $this;
    }

    /**
     * Get the current template name.
     *
     * @return string The template name.
     */
    public function getTpl()
    {
        return $this->_sTplName;
    }

    /**
     * Get the current module template name.
     *
     * @return string The template name.
     */
    public function getModTpl()
    {
        return $this->_sModTplName;
    }

    /**
     * @return object $this
     * @throws \PH7\Framework\Layout\Exception If the template file is not found.
     */
    public function tpl()
    {
        if (!empty($this->_sUserTpl) && $this->_oConfig->load(PH7_PATH_TPL . $this->_sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sTplName = $this->_sUserTpl;
        }
        else if ($this->_oConfig->load(PH7_PATH_TPL . $this->_sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sTplName = $this->_sDefaultTpl;
        }
        else if ($this->_oConfig->load(PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sTplName = PH7_DEFAULT_THEME;
        }
        else
        {
            throw new Exception('Template file not found! File: \'' . PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' doesn\'t exist.');
        }

        return $this;
    }

    /**
     * @return object $this
     * @throws \PH7\Framework\Layout\Exception If the template module file is not found.
     */
    public function modTpl()
    {
        $oRegistry = \PH7\Framework\Registry\Registry::getInstance();

        if ($this->_oConfig->load($oRegistry->path_module_views . $this->_sUserTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sModTplName = $this->_sUserTpl;
        }
        else if ($this->_oConfig->load($oRegistry->path_module_views . $this->_sDefaultTpl . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sModTplName = $this->_sDefaultTpl;
        }
        else if ($this->_oConfig->load($oRegistry->path_module_views . PH7_DEFAULT_TPL_MOD . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->_sModTplName = PH7_DEFAULT_TPL_MOD;
        }
        else
        {
            throw new Exception('Template module file not found! File: \'' . $oRegistry->path_module_views . PH7_DEFAULT_TPL_MOD . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' doesn\'t exist.');
        }

        unset($oRegistry);

        return $this;
    }

    public function __destruct()
    {
        unset($this->_oConfig, $this->_sDefaultTpl, $this->_sUserTpl, $this->_sTplName, $this->_sModTplName);
    }

}
