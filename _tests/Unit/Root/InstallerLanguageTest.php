<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

require_once __DIR__ . '/../../../_install/library/Language.class.php';

use PH7\Language;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class InstallerLanguageTest extends TestCase
{
    private const LOCKDOWN_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/18.x/docs/QUICK_START.md#6-lock-the-installation-down';

    #[DataProvider('languageInputProvider')]
    public function testOnlyTwoLetterLanguageCodesAreAccepted(mixed $mInput, string $sExpected): void
    {
        $oMethod = new ReflectionMethod(Language::class, 'normalizeLanguage');

        $this->assertSame($sExpected, $oMethod->invoke(null, $mInput));
    }

    public static function languageInputProvider(): array
    {
        return [
            ['en', 'en'],
            [' FR ', 'fr'],
            ['../../en', ''],
            ['en/../fr', ''],
            [['en'], ''],
            [null, '']
        ];
    }

    #[DataProvider('installerLanguageProvider')]
    public function testFinishStepRequiresProductionLockdown(string $sLanguage): void
    {
        $sLanguageFile = __DIR__ . '/../../../_install/langs/' . $sLanguage . '/install.lang.php';
        $sContents = file_get_contents($sLanguageFile);

        $this->assertIsString($sContents);
        $this->assertSame(2, substr_count($sContents, 'Controller::SOFTWARE_LOCKDOWN_URL'));
        $this->assertStringContainsString('rel="noopener"', $sContents);
    }

    public static function installerLanguageProvider(): array
    {
        return [
            ['en'],
            ['es'],
            ['fr']
        ];
    }

    public function testLockdownDocumentationUrlTargetsQuickStartStep(): void
    {
        $sController = file_get_contents(__DIR__ . '/../../../_install/library/Controller.class.php');

        $this->assertIsString($sController);
        $this->assertStringContainsString(self::LOCKDOWN_URL, $sController);
    }

    #[DataProvider('installerLanguageProvider')]
    public function testInstallerLicenseCreditsContributors(string $sLanguage): void
    {
        $sLicense = file_get_contents(
            __DIR__ . '/../../../_install/langs/' . $sLanguage . '/license.html'
        );

        $this->assertIsString($sLicense);
        $this->assertStringContainsString(
            'Pierre-Henry Soria and pH7Builder contributors',
            $sLicense
        );
    }
}
