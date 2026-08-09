<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / App / System / Core / Forms
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

require_once PH7_PATH_SYS . 'core/forms/ConfigFileCoreForm.php';
require_once PH7_PATH_SYS . 'core/forms/processing/ConfigFileCoreFormProcess.php';

use PH7\ConfigFileCoreForm;
use PH7\ConfigFileCoreFormProcess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ConfigFileCoreFormTest extends TestCase
{
    #[DataProvider('sensitiveKeyProvider')]
    public function testSensitiveConfigurationKeysAreDetected(string $sKey): void
    {
        $this->assertTrue(ConfigFileCoreForm::isSensitiveKey($sKey));
    }

    public static function sensitiveKeyProvider(): array
    {
        return [
            ['stripe.secret_key'],
            ['2co.secret_word'],
            ['braintree.private_key'],
            ['twilio.api_token'],
            ['youtube.key'],
            ['password']
        ];
    }

    public function testPublicConfigurationKeysRemainVisible(): void
    {
        $this->assertFalse(ConfigFileCoreForm::isSensitiveKey('stripe.publishable_key'));
        $this->assertFalse(ConfigFileCoreForm::isSensitiveKey('braintree.public_key'));
        $this->assertFalse(ConfigFileCoreForm::isSensitiveKey('paypal.email'));
    }

    public function testSensitiveFieldNeverReceivesTheStoredValue(): void
    {
        $oMethod = new ReflectionMethod(ConfigFileCoreForm::class, 'getFieldProperties');

        $aProperties = $oMethod->invoke(null, 'stripe.secret_key', 'stored-secret');

        $this->assertSame('', $aProperties['value']);
        $this->assertSame('Configured', $aProperties['placeholder']);
        $this->assertSame('new-password', $aProperties['autocomplete']);
    }

    #[DataProvider('unavailableGatewayHelpProvider')]
    public function testUnavailableGatewaysExplainTheirRequiredMigration(string $sKey, string $sExpectedHelp): void
    {
        $oMethod = new ReflectionMethod(ConfigFileCoreForm::class, 'getFieldProperties');

        $aProperties = $oMethod->invoke(null, $sKey, 1);

        $this->assertStringContainsString($sExpectedHelp, $aProperties['description']);
    }

    public static function unavailableGatewayHelpProvider(): array
    {
        return [
            'Stripe' => ['stripe.enabled', 'Checkout Sessions or Payment Intents'],
            '2Checkout' => ['2co.enabled', '2Checkout API 6.0']
        ];
    }

    public function testBlankSensitiveSubmissionPreservesExistingValue(): void
    {
        $oMethod = new ReflectionMethod(ConfigFileCoreFormProcess::class, 'shouldPreserveExistingValue');

        $this->assertTrue($oMethod->invoke(null, 'stripe.secret_key', ''));
        $this->assertFalse($oMethod->invoke(null, 'stripe.secret_key', 'replacement'));
        $this->assertFalse($oMethod->invoke(null, 'currency_sign', ''));
    }

    public function testQuotedConfigValueIsSafelyReplaced(): void
    {
        $sConfig = "[module.setting]\nstripe.secret_key = \"\"\n";
        $oMethod = new ReflectionMethod(ConfigFileCoreFormProcess::class, 'replaceConfigValue');

        $sSecret = 'secret"with\\symbols${PH7_CONFIG_EXPANSION}';
        putenv('PH7_CONFIG_EXPANSION=must-not-be-expanded');
        $sUpdatedConfig = $oMethod->invoke(null, $sConfig, 'stripe.secret_key', $sSecret);
        $aParsedConfig = parse_ini_string($sUpdatedConfig, true);
        putenv('PH7_CONFIG_EXPANSION');

        $this->assertIsArray($aParsedConfig);
        $this->assertSame($sSecret, $aParsedConfig['module.setting']['stripe.secret_key']);
    }
}
