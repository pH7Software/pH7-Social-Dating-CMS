<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined\Variable as PredefinedVariable;
use PHPUnit_Framework_TestCase;

class VariableTest extends PHPUnit_Framework_TestCase
{
    public function testSoftwareNameVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_name}');
        $this->assertAttributeSame(
            '{software_name}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_NAME?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareNameWithTalVariableDelimiters()
    {
        $oPredefinedVar = new PredefinedVariable('[[software_name]]');
        $oPredefinedVar->setLeftDelimiter('[[');
        $oPredefinedVar->setRightDelimiter(']]');

        $this->assertAttributeSame(
            '[[software_name]]',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_NAME?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareUrlVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_url}');
        $this->assertAttributeSame(
            '{software_url}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_WEBSITE?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareDocUrlVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_doc_url}');
        $this->assertAttributeSame(
            '{software_doc_url}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_DOC_URL?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareIssueUrlVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_issue_url}');
        $this->assertAttributeSame(
            '{software_issue_url}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_ISSUE_URL?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareReviewUrlVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_review_url}');
        $this->assertAttributeSame(
            '{software_review_url}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_REVIEW_URL?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testSoftwareVersionVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{software_version}');
        $this->assertAttributeSame(
            '{software_version}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo self::SOFTWARE_VERSION?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlRootVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_root}');
        $this->assertAttributeSame(
            '{url_root}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->site_url?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlRelativeVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_relative}');
        $this->assertAttributeSame(
            '{url_relative}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->url_relative?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testCurrentUrlVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{current_url}');
        $this->assertAttributeSame(
            '{current_url}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->httpRequest->currentUrl()?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlAdminModVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_admin_mod}');
        $this->assertAttributeSame(
            '{url_admin_mod}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_ADMIN_MOD . PH7_SH?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlStaticVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_static}');
        $this->assertAttributeSame(
            '{url_static}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_STATIC?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlStaticCssVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_static_css}');
        $this->assertAttributeSame(
            '{url_static_css}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_STATIC . PH7_CSS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlStaticImgVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_static_img}');
        $this->assertAttributeSame(
            '{url_static_img}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_STATIC . PH7_IMG?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlStaticJsVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_static_js}');
        $this->assertAttributeSame(
            '{url_static_js}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_STATIC . PH7_JS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlDataVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_data}');
        $this->assertAttributeSame(
            '{url_data}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_DATA?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlDataSysVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_data_sys}');
        $this->assertAttributeSame(
            '{url_data_sys}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_DATA_SYS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlDataModVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_data_mod}');
        $this->assertAttributeSame(
            '{url_data_mod}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_DATA_MOD?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlDataSysModVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_data_sys_mod}');
        $this->assertAttributeSame(
            '{url_data_sys_mod}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_DATA_SYS_MOD?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl}');
        $this->assertAttributeSame(
            '{url_tpl}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplCssVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_css}');
        $this->assertAttributeSame(
            '{url_tpl_css}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplImgVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_img}');
        $this->assertAttributeSame(
            '{url_tpl_img}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_IMG?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplJsVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_js}');
        $this->assertAttributeSame(
            '{url_tpl_js}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplModVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_mod}');
        $this->assertAttributeSame(
            '{url_tpl_mod}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplModCssVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_mod_css}');
        $this->assertAttributeSame(
            '{url_tpl_mod_css}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplModImgVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_mod_img}');
        $this->assertAttributeSame(
            '{url_tpl_mod_img}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_IMG?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testUrlTplModJsVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{url_tpl_mod_js}');
        $this->assertAttributeSame(
            '{url_tpl_mod_js}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo $this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS?>',
            $oPredefinedVar->assign()->get()
        );
    }

    public function testIpVariable()
    {
        $oPredefinedVar = new PredefinedVariable('{ip}');
        $this->assertAttributeSame(
            '{ip}',
            'sCode',
            $oPredefinedVar
        );
        $this->assertSame(
            '<?php echo Framework\Ip\Ip::get()?>',
            $oPredefinedVar->assign()->get()
        );
    }
}
