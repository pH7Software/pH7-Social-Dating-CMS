<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Define variables.
 *                   Predefined variables can save considerable resources and speeds up the code with respect to variables assigned through the object's template engine (PH7Tpl).
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

defined('PH7') or exit('Restricted access');

class Variable extends Predefined
{
    /**
     * {@inheritdoc}
     */
    public function assign()
    {
        $this->kernelVariables();

        $this->mainUrlVariables();

        $this->staticUrlVariables();

        $this->dataUrlVariables();

        $this->systemTemplateUrlVariables();

        $this->moduleTemplateUrlVariables();

        $this->globalVariables();

        return $this;
    }

    private function kernelVariables()
    {
        $this->addVar('software_name', 'self::SOFTWARE_NAME');
        $this->addVar('software_url', 'self::SOFTWARE_WEBSITE');
        $this->addVar('software_doc_url', 'self::SOFTWARE_DOC_URL');
        $this->addVar('software_issue_url', 'self::SOFTWARE_ISSUE_URL');
        $this->addVar('software_review_url', 'self::SOFTWARE_REVIEW_URL');
        $this->addVar('software_version', 'self::SOFTWARE_VERSION');
    }

    private function mainUrlVariables()
    {
        $this->addVar('url_root', '$this->registry->site_url');
        $this->addVar('url_relative', '$this->registry->url_relative');
        $this->addVar('current_url', '$this->httpRequest->currentUrl()');
        $this->addVar('url_admin_mod', 'PH7_ADMIN_MOD . PH7_SH');
    }

    private function staticUrlVariables()
    {
        $this->addVar('url_static', 'PH7_URL_STATIC');
        $this->addVar('url_static_css', 'PH7_URL_STATIC . PH7_CSS');
        $this->addVar('url_static_img', 'PH7_URL_STATIC . PH7_IMG');
        $this->addVar('url_static_js', 'PH7_URL_STATIC . PH7_JS');
    }

    private function dataUrlVariables()
    {
        $this->addVar('url_data', 'PH7_URL_DATA');
        $this->addVar('url_data_sys', 'PH7_URL_DATA_SYS');
        $this->addVar('url_data_mod', 'PH7_URL_DATA_MOD');
        $this->addVar('url_data_sys_mod', 'PH7_URL_DATA_SYS_MOD');
    }

    private function systemTemplateUrlVariables()
    {
        $this->addVar('url_tpl', 'PH7_URL_TPL . PH7_TPL_NAME . PH7_SH');
        $this->addVar('url_tpl_css', 'PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS');
        $this->addVar('url_tpl_img', 'PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG');
        $this->addVar('url_tpl_js', 'PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS');
    }

    private function moduleTemplateUrlVariables()
    {
        $this->addVar('url_tpl_mod', '$this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH');
        $this->addVar('url_tpl_mod_css', '$this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS');
        $this->addVar('url_tpl_mod_img', '$this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_IMG');
        $this->addVar('url_tpl_mod_js', '$this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS');
    }

    private function globalVariables()
    {
        $this->addVar('ip', 'Framework\Ip\Ip::get()');
    }
}
