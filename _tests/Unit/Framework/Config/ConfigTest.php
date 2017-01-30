<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

namespace PH7\Test\Unit\Framework\Config;

use PH7\Framework\Config\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $oConfig;

    protected function setUp()
    {
        $this->oConfig = Config::getInstance();
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testSetDuplicateKey()
    {
        $this->oConfig->setValue('key1', 'Blablabla');
        $this->oConfig->setValue('key1', 'Nananana'); // Duplicate key
    }

    public function testGetValue()
    {
        $sName = 'Pierre-Henry Soria';
        $this->oConfig->setValue('name', $sName);
        $this->assertEquals($sName, $this->oConfig->getValue('name'));
    }

    public function testInvalidLoad()
    {
        $this->assertFalse($this->oConfig->load('invalid_path/config.ini'));
    }

    public function testValidLoad()
    {
        $this->assertTrue($this->oConfig->load(PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE));
    }

    public function testDefaultIniValues()
    {
        $this->assertEquals('base', $this->oConfig->values['application']['default_theme']);
        $this->assertEquals('en_US', $this->oConfig->values['application']['default_lang']);
        $this->oConfig->setValue('ph7cms', 'pH7 Social Dating CMS');
        $this->assertEquals('production', $this->oConfig->values['mode']['environment']);
        $this->assertEquals('pH7 Social Dating CMS', $this->oConfig->getValue('ph7cms'));
    }
 }
