<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

final class ThemePreviewTest extends TestCase
{
    public function testPreviewUsesPluginMarkupAndAnchoredDropdowns(): void
    {
        $sPreview = file_get_contents(dirname(PH7_PATH_PROTECTED) . '/_tools/theme-preview.html');
        self::assertIsString($sPreview);
        $oDocument = new DOMDocument;
        self::assertTrue($oDocument->loadHTML($sPreview, LIBXML_NOERROR | LIBXML_NOWARNING));
        $oXPath = new DOMXPath($oDocument);

        self::assertSame(1, $oXPath->query('//div[@class="apprise-inner"]/following-sibling::div[@class="apprise-buttons"]')->length);
        self::assertSame(2, $oXPath->query('//div[@class="apprise-buttons"]/button[not(@class)]')->length);
        self::assertSame(1, $oXPath->query('//div[@class="dropdown open"]/button[@aria-expanded="true"]/following-sibling::ul[@class="dropdown-menu"]')->length);
        self::assertSame(0, $oXPath->query('//ul[@class="dropdown-menu" and contains(@style, "position:static")]')->length);
    }
}
