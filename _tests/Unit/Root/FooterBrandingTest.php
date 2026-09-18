<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly;
use PH7\Framework\Mvc\Model\DbConfig;
use PHPUnit\Framework\TestCase;

final class FooterBrandingTest extends TestCase
{
    protected function setUp(): void
    {
        class_alias(FooterSettingsStub::class, DbConfig::class);
        FooterSettingsStub::setDisplayed(true);
    }

    public function testThemeFootersRenderOnlyTheVersionedProjectCredit(): void
    {
        foreach (['base', 'premium'] as $sTheme) {
            $sTemplate = file_get_contents(dirname(PH7_PATH_PROTECTED) . '/templates/themes/' . $sTheme . '/tpl/layout.tpl');
            self::assertIsString($sTemplate);
            self::assertStringNotContainsString('We use GeoLite2', $sTemplate);
            self::assertStringNotContainsString('maxmind.com', $sTemplate);
            self::assertSame(1, preg_match('/<div class="ft_copy">.*?<p>(.*?)<\/p>/s', $sTemplate, $aMatches));
            self::assertStringNotContainsString('{site_name}', $aMatches[1]);

            $oSyntax = new Curly();
            $oSyntax->setCode($aMatches[1]);
            $oSyntax->parse();
            $design = new Design();
            ob_start();
            try {
                eval('?>' . $oSyntax->getParsedCode());
                $sHtml = (string)ob_get_contents();
            } finally {
                ob_end_clean();
            }

            $oDocument = new \DOMDocument();
            $oDocument->loadHTML('<div>' . $sHtml . '</div>');
            self::assertSame('Powered by pH7Builder v' . Kernel::SOFTWARE_VERSION, trim($oDocument->textContent));
            self::assertSame(1, $oDocument->getElementsByTagName('a')->length);
            self::assertSame(Kernel::SOFTWARE_GIT_REPO_URL, $oDocument->getElementsByTagName('a')->item(0)->getAttribute('href'));
            self::assertSame(0, $oDocument->getElementsByTagName('strong')->length);
            self::assertStringNotContainsString('italic', $sHtml);
        }
    }

    public function testBrandingVisibilitySettingIsPreserved(): void
    {
        FooterSettingsStub::setDisplayed(false);
        $sHtml = $this->renderLink([true, true, true]);

        self::assertSame('', trim(strip_tags($sHtml)));
        self::assertStringNotContainsString('<a', $sHtml);
        self::assertStringContainsString('created by ' . Kernel::SOFTWARE_AUTHOR, $sHtml);
    }

    public function testEmailContextDoesNotAddVisibleBranding(): void
    {
        self::assertSame('', $this->renderLink([true, true, true, false, true]));
    }

    public function testPlainTextCreditRemainsAvailable(): void
    {
        self::assertSame('Powered by pH7Builder v' . Kernel::SOFTWARE_VERSION, $this->renderLink([false, true, true, false]));
    }

    public function testVersionCanStillBeOmittedByOtherCallers(): void
    {
        self::assertSame('Powered by pH7Builder', strip_tags($this->renderLink([true, true, false, false])));
    }

    public function testMaxMindAttributionRemainsOutsideTheFooter(): void
    {
        foreach (['COPYRIGHT.md', '_protected/app/system/modules/page/views/base/tpl/main/legalnotice.tpl'] as $sFile) {
            $sNotice = file_get_contents(dirname(PH7_PATH_PROTECTED) . '/' . $sFile);
            self::assertIsString($sNotice);
            self::assertStringContainsString('GeoLite2 data created by', $sNotice);
            self::assertStringContainsString('https://www.maxmind.com/', $sNotice);
            self::assertStringContainsString('https://creativecommons.org/licenses/by-sa/4.0/', $sNotice);
        }
    }

    private function renderLink(array $aArguments): string
    {
        ob_start();
        try {
            (new Design())->link(...$aArguments);

            return (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}

final class FooterSettingsStub
{
    private static bool $bDisplayed = true;

    public static function setDisplayed(bool $bDisplayed): void
    {
        self::$bDisplayed = $bDisplayed;
    }

    public static function getSetting(string $sSetting): bool
    {
        return $sSetting === 'displayPoweredByLink' && self::$bDisplayed;
    }
}
