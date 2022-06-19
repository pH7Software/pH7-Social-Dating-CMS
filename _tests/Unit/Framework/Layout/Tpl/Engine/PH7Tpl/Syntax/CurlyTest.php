<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly as CurlySyntax;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;

class CurlyTest extends SyntaxTestCase
{
    protected const INPUT_DIR = 'input/curly/';
    protected const OUTPUT_DIR = 'output/curly/';
    protected const INPUT_TPL_FILE_EXT = '.curly.tpl';
    protected const OUTPUT_PHP_FILE_EXT = '.curly.output';

    private CurlySyntax $oCurlySyntax;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oCurlySyntax = new CurlySyntax;
    }

    public function testParseUnsetCode(): void
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::CURLY_SYNTAX);

        $this->oCurlySyntax->parse();
    }

    public function testAutoIncludeStatement(): void
    {
        $this->assertFile('auto-include', $this->oCurlySyntax);
    }

    public function testIncludeStatement(): void
    {
        $this->assertFile('include', $this->oCurlySyntax);
    }

    public function testMainIncludeStatement(): void
    {
        $this->assertFile('main-include', $this->oCurlySyntax);
    }

    public function testDefMainAutoIncludeStatement(): void
    {
        $this->assertFile('def-main-auto-include', $this->oCurlySyntax);
    }

    public function testDefMainIncludeStatement(): void
    {
        $this->assertFile('def-main-include', $this->oCurlySyntax);
    }

    public function testManualIncludeStatement(): void
    {
        $this->assertFile('manual-include', $this->oCurlySyntax);
    }

    public function testPhpCode(): void
    {
        $this->assertFile('php-code', $this->oCurlySyntax);
    }

    public function testPhpCodeWithSemicolon(): void
    {
        $this->assertFile('php-code-semicolon', $this->oCurlySyntax);
    }

    public function testEcho(): void
    {
        $this->assertFile('echo', $this->oCurlySyntax);
    }

    public function testEchoWithSemicolon(): void
    {
        $this->assertFile('echo-semicolon', $this->oCurlySyntax);
    }

    public function testIfStatement(): void
    {
        $this->assertFile('if', $this->oCurlySyntax);
    }

    public function testElseifStatement(): void
    {
        $this->assertFile('elseif', $this->oCurlySyntax);
    }

    public function testElseStatement(): void
    {
        $this->assertFile('else', $this->oCurlySyntax);
    }

    public function testForLoop(): void
    {
        $this->assertFile('for', $this->oCurlySyntax);
    }

    public function testWhileLoop(): void
    {
        $this->assertFile('while', $this->oCurlySyntax);
    }

    public function testEachLoop(): void
    {
        $this->assertFile('each', $this->oCurlySyntax);
    }

    public function testDesignModelObject(): void
    {
        $this->assertFile('design-model', $this->oCurlySyntax);
    }

    public function testEscapeFunction(): void
    {
        $this->assertFile('escape', $this->oCurlySyntax);
    }

    public function testInlineLangFunction(): void
    {
        $this->assertFile('lang-inline', $this->oCurlySyntax);
    }

    public function testLangFunction(): void
    {
        $this->assertFile('lang', $this->oCurlySyntax);
    }

    public function testLiteralFunction(): void
    {
        $this->assertFile('literal', $this->oCurlySyntax);
    }

    public function testVariable(): void
    {
        $this->assertFile('variable', $this->oCurlySyntax);
    }

    public function testObjectShortcuts(): void
    {
        $this->assertFile('shortcuts', $this->oCurlySyntax);
    }

    public function testSingleLineComment(): void
    {
        $this->assertFile('comment-single-line', $this->oCurlySyntax);
    }

    public function testComment(): void
    {
        $this->assertFile('comment', $this->oCurlySyntax);
    }

    protected function getInputDirectory(): string
    {
        return self::INPUT_DIR;
    }

    protected function getOutputDirectory(): string
    {
        return self::OUTPUT_DIR;
    }

    protected function getInputTemplateFileExtension(): string
    {
        return self::INPUT_TPL_FILE_EXT;
    }

    protected function getOutputPhpFileExtension(): string
    {
        return self::OUTPUT_PHP_FILE_EXT;
    }
}
