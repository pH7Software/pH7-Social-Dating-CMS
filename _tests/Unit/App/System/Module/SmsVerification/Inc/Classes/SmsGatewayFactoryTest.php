<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Sms Verification / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvider.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvidable.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/ClickatellProvider.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/TwilioProvider.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/InvalidSmsGatewayException.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsGatewayFactory.php';

use PH7\ClickatellProvider;
use PH7\InvalidSmsGatewayException;
use PH7\SmsGatewayFactory;
use PH7\TwilioProvider;
use PH7\Framework\Config\Config;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class SmsGatewayFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setConfigInstance($this->createConfigMock());
    }

    protected function tearDown(): void
    {
        $this->setConfigInstance(null);
        parent::tearDown();
    }

    public function testCreateReturnsClickatellProvider(): void
    {
        $this->assertInstanceOf(ClickatellProvider::class, SmsGatewayFactory::create('clickatell'));
    }

    public function testCreateReturnsTwilioProvider(): void
    {
        $this->assertInstanceOf(TwilioProvider::class, SmsGatewayFactory::create('twilio'));
    }

    public function testCreateThrowsOnInvalidGateway(): void
    {
        $this->expectException(InvalidSmsGatewayException::class);
        SmsGatewayFactory::create('unsupported-gateway');
    }

    private function createConfigMock(): Config
    {
        $oReflection = new ReflectionClass(Config::class);

        /** @var Config $oConfig */
        $oConfig = $oReflection->newInstanceWithoutConstructor();
        $oConfig->values = [
            'module.setting' => [
                'clickatell.sender.phone_number' => '+61400000000',
                'clickatell.api_token' => 'clickatell-token',
                'twilio.sender.phone_number' => '+61400000000',
                'twilio.api_token' => 'twilio-token',
                'twilio.api_id' => 'twilio-api-id',
            ],
        ];

        return $oConfig;
    }

    private function setConfigInstance(?Config $oConfig): void
    {
        $oReflection = new ReflectionClass(Config::class);
        $oInstanceProp = $oReflection->getProperty('oInstance');
        $oInstanceProp->setValue(null, $oConfig);
    }
}
