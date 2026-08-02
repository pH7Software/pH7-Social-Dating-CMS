<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Payment / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'payment/inc/class/Api.php';
require_once PH7_PATH_SYS_MOD . 'payment/inc/class/Braintree.php';

use Braintree\Configuration as GatewayConfiguration;
use PH7\Braintree;
use PH7\Framework\Config\Config;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

final class BraintreeTest extends TestCase
{
    protected function tearDown(): void
    {
        GatewayConfiguration::reset();
    }

    public function testGetConfigurationClassResolvesNamespacedClass(): void
    {
        $oMethod = new ReflectionMethod(Braintree::class, 'getConfigurationClass');

        $this->assertSame(GatewayConfiguration::class, $oMethod->invoke(null));
    }

    public function testInitSetsProductionEnvironmentByDefault(): void
    {
        $oConfig = $this->createConfig(false, 'merchant_prod');

        Braintree::init($oConfig);

        $this->assertSame('production', GatewayConfiguration::environment());
        $this->assertSame('merchant_prod', GatewayConfiguration::merchantId());
        $this->assertSame('public_key', GatewayConfiguration::publicKey());
        $this->assertSame('private_key', GatewayConfiguration::privateKey());
    }

    public function testInitSetsSandboxEnvironmentWhenSandboxEnabled(): void
    {
        $oConfig = $this->createConfig(true, 'merchant_prod');

        Braintree::init($oConfig);

        $this->assertSame('sandbox', GatewayConfiguration::environment());
    }

    public function testInitSetsSandboxEnvironmentForSandboxMerchantId(): void
    {
        $oConfig = $this->createConfig(false, Braintree::SANDBOX_MERCHANT_ID);

        Braintree::init($oConfig);

        $this->assertSame('sandbox', GatewayConfiguration::environment());
    }

    public function testInitSupportsLegacyPrivateKeySetting(): void
    {
        $oConfig = $this->createConfig(false, 'merchant_prod');
        $oConfig->values['module.setting']['braintree.private_ke'] =
            $oConfig->values['module.setting']['braintree.private_key'];
        unset($oConfig->values['module.setting']['braintree.private_key']);

        Braintree::init($oConfig);

        $this->assertSame('private_key', GatewayConfiguration::privateKey());
    }

    private function createConfig(bool $bSandboxEnabled, string $sMerchantId): Config
    {
        $oReflection = new ReflectionClass(Config::class);

        /** @var Config $oConfig */
        $oConfig = $oReflection->newInstanceWithoutConstructor();
        $oConfig->values = [
            'module.setting' => [
                'sandbox.enabled' => $bSandboxEnabled,
                'braintree.merchant_id' => $sMerchantId,
                'braintree.public_key' => 'public_key',
                'braintree.private_key' => 'private_key',
            ],
        ];

        return $oConfig;
    }
}
