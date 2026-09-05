<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Compress;

use PH7\Framework\Compress\Compress;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CompressCssTest extends TestCase
{
    #[DataProvider('quotedCommentProvider')]
    public function testQuotesInsideCommentsDoNotConsumeCssRules(string $sFirstComment, string $sLastComment): void
    {
        $sCss = $sFirstComment . "\n.pfbc-textbox { min-height: 44px; font-size: 16px; }\n" . $sLastComment;
        $sMinified = $this->compressCss($sCss);

        $this->assertStringContainsString('.pfbc-textbox{min-height:44px;font-size:16px}', $sMinified);
        $this->assertStringNotContainsString('/*', $sMinified);
    }

    public static function quotedCommentProvider(): iterable
    {
        yield 'apostrophes' => ["/* Bootstrap's controls */", "/* form.css's selectors */"];
        yield 'double quotes' => ['/* An unmatched " in a comment */', '/* Another " comment */'];
        yield 'quoted values in comments' => ['/* "Quoted" and \'quoted\' comments */', '/* End */'];
    }

    public function testCompressionPreservesQuotedValues(): void
    {
        $sMinified = $this->compressCss('.label::after { content: "Your name"; }');

        $this->assertStringContainsString('content:"Your name"', $sMinified);
    }

    public function testProductionDesignSystemRetainsPfbcRules(): void
    {
        $sCss = file_get_contents(dirname(__DIR__, 4) . '/templates/themes/base/css/design_system.css');
        $this->assertIsString($sCss);
        $sMinified = $this->compressCss($sCss);

        foreach (['input.pfbc-textbox', '.pfbc-checkbox', '.pfbc-radio', '.pfbc-error.ui-state-error', '.pwd_field'] as $sSelector) {
            $this->assertStringContainsString($sSelector, $sMinified);
        }
        $this->assertStringContainsString('min-height:44px', $sMinified);
        $this->assertStringContainsString('font-size:16px', $sMinified);
    }

    private function compressCss(string $sCss): string
    {
        // Exercise the default PHP path without configuring optional external compilers.
        $oCompress = (new ReflectionClass(Compress::class))->newInstanceWithoutConstructor();

        return $oCompress->parseCss($sCss);
    }
}
