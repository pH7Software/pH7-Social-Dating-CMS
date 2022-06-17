<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Config;

use PH7\Framework\Config\Config;
use PH7\Framework\Config\KeyAlreadyExistsException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $oConfig;

    protected function setUp(): void
    {
        $this->oConfig = Config::getInstance();

        // Load the testing config.ini file for each test
        $this->oConfig->load(PH7_PATH_TEST . 'fixtures/' . PH7_CONFIG_FILE);
    }

    public function testSetDuplicateKey(): void
    {
        $this->expectException(KeyAlreadyExistsException::class);

        $this->oConfig->setValue('key1', 'Blablabla');
        $this->oConfig->setValue('key1', 'Nananana'); // Duplicate key
    }

    public function testGetValue(): void
    {
        $sName = 'Pierre-Henry Soria';

        $this->oConfig->setValue('name', $sName);
        $this->assertSame($sName, $this->oConfig->getValue('name'));
    }

    public function testInvalidLoad(): void
    {
        $this->assertFalse($this->oConfig->load('invalid_path/config.ini'));
    }

    public function testValidLoad(): void
    {
        $this->assertTrue($this->oConfig->load(PH7_PATH_TEST . 'fixtures/' . PH7_CONFIG_FILE));
    }

    public function testValueIsCasted(): void
    {
        $this->assertIsBool($this->oConfig->values['test']['enabled']);
    }

    public function testDefaultIniValues(): void
    {
        $this->assertSame('base', $this->oConfig->values['application']['default_theme']);
        $this->assertSame('en_US', $this->oConfig->values['application']['default_lang']);
        $this->assertSame('production', $this->oConfig->values['mode']['environment']);
    }
}
