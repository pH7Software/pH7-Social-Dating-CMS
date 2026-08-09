<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class LaunchSafetyDefaultsTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testFreshPaymentConfigUsesSafeEmptyDefaults(): void
    {
        $aConfig = parse_ini_file(
            self::PROJECT_ROOT . '/_protected/app/system/modules/payment/config/config.ini',
            true
        );
        $this->assertIsArray($aConfig);
        $aSettings = $aConfig['module.setting'];

        $this->assertSame('1', (string)$aSettings['sandbox.enabled']);
        $this->assertSame('0', (string)$aSettings['vat_rate']);
        foreach (['paypal', 'stripe', 'braintree', '2co'] as $sProvider) {
            $this->assertSame('0', (string)$aSettings[$sProvider . '.enabled']);
        }
        foreach (
            [
                'paypal.email',
                'stripe.publishable_key',
                'stripe.secret_key',
                'braintree.merchant_id',
                'braintree.public_key',
                'braintree.private_key',
                '2co.vendor_id',
                '2co.secret_word'
            ] as $sSetting
        ) {
            $this->assertSame('', $aSettings[$sSetting]);
        }

        $sConfigForm = $this->readProjectFile(
            '_protected/app/system/core/forms/ConfigFileCoreForm.php'
        );
        $this->assertStringContainsString("\$sKey === 'vat_rate'", $sConfigForm);
        $this->assertStringContainsString('Tax is specific to your business and jurisdiction.', $sConfigForm);
    }

    public function testFreshPaidMembershipsRemainDisabledUntilConfigured(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        foreach ([4, 5, 6] as $iMembershipId) {
            $this->assertMatchesRegularExpression(
                '/\(' . $iMembershipId . ',[^\n]+, [0-9]+\.[0-9]{2}, [0-9]+, \'0\'\)/',
                $sSchema
            );
        }
    }

    public function testDockerImageBuildsEveryComposerRequiredNonCoreExtension(): void
    {
        $sDockerfile = $this->readProjectFile('Dockerfile');

        $this->assertStringContainsString('libcurl4-openssl-dev', $sDockerfile);
        $this->assertStringContainsString('libxml2-dev', $sDockerfile);
        $this->assertMatchesRegularExpression(
            '/docker-php-ext-install[^\n]+curl[^\n]+dom[^\n]+exif[^\n]+gd[^\n]+mbstring[^\n]+pdo_mysql[^\n]+xml[^\n]+zip/',
            $sDockerfile
        );
    }

    public function testFreshHomepageCopyIsNeutralAndOwnerEditable(): void
    {
        foreach (
            [
                '_install/data/sql/MySQL/pH7_Core.sql',
                '_protected/framework/Seo/Data/MetaData.class.php'
            ] as $sFile
        ) {
            $sContents = $this->readProjectFile($sFile);

            $this->assertStringContainsString('Welcome to our community', $sContents);
            $this->assertStringNotContainsString('The Best Online Social Dating Service', $sContents);
            $this->assertStringNotContainsString('the #1', $sContents);
        }
    }

    public function testFreshForumCopyIsNeutralAndUseful(): void
    {
        $sSchema = $this->readProjectFile('_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertStringContainsString("'Introductions'", $sSchema);
        $this->assertStringContainsString("'Dating and Relationships'", $sSchema);
        $this->assertStringContainsString("'Community Feedback'", $sSchema);
        $this->assertStringNotContainsString("'The Best Dating Site'", $sSchema);
        $this->assertStringNotContainsString("'Free Online Dating Site'", $sSchema);
    }

    public function testApplicationSessionUsesStrictSecureCookieDefaults(): void
    {
        $sSession = $this->readProjectFile('_protected/framework/Session/Session.class.php');

        $this->assertStringContainsString("ini_set('session.use_strict_mode', '1')", $sSession);
        $this->assertStringContainsString("'secure' => Server::isHttps()", $sSession);
        $this->assertStringContainsString("'httponly' => true", $sSession);
        $this->assertStringContainsString("'samesite' => 'Lax'", $sSession);
        $this->assertStringContainsString('Check session.save_path permissions.', $sSession);
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . '/' . $sRelativePath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
