<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Compress / Minify
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Compress\Minify;

use ErrorException;
use PH7\Framework\Compress\Minify\Css;
use PHPUnit\Framework\TestCase;

final class CssTest extends TestCase
{
    public function testFontFamilyMinificationDoesNotEmitPhpDeprecation(): void
    {
        set_error_handler(
            static function (int $iSeverity, string $sMessage, string $sFile, int $iLine): bool {
                if ($iSeverity === E_DEPRECATED) {
                    throw new ErrorException($sMessage, 0, $iSeverity, $sFile, $iLine);
                }

                return false;
            }
        );

        try {
            $sMinifiedCss = Css::process('body { font-family: Open Sans, sans-serif; }');
        } finally {
            restore_error_handler();
        }

        $this->assertStringContainsString('font-family:Open Sans,sans-serif', $sMinifiedCss);
    }
}
