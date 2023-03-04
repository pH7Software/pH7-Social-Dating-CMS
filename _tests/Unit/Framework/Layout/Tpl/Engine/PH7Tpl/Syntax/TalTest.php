<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Tal as TalSyntax;

class TalTest extends SyntaxTestCase
{
    protected const INPUT_DIR = 'input/tal/';
    protected const OUTPUT_DIR = 'output/tal/';
    protected const INPUT_TPL_FILE_EXT = '.tal.tpl';
    protected const OUTPUT_PHP_FILE_EXT = '.tal.output';

    private TalSyntax $oTalSyntax;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oTalSyntax = new TalSyntax;
    }

    public function testParseUnsetCode(): void
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::TAL_SYNTAX);

        $this->oTalSyntax->parse();
    }

    public function testAutoIncludeStatement(): void
    {
        $this->assertFile('auto-include', $this->oTalSyntax);
    }

    public function testIncludeStatement(): void
    {
        $this->assertFile('include', $this->oTalSyntax);
    }

    public function testMainIncludeStatement(): void
    {
        $this->assertFile('main-include', $this->oTalSyntax);
    }

    public function testDefMainAutoIncludeStatement(): void
    {
        $this->assertFile('def-main-auto-include', $this->oTalSyntax);
    }

    public function testDefMainIncludeStatement(): void
    {
        $this->assertFile('def-main-include', $this->oTalSyntax);
    }

    public function testManualIncludeStatement(): void
    {
        $this->assertFile('manual-include', $this->oTalSyntax);
    }

    public function testPhpCode(): void
    {
        $this->assertFile('php-code', $this->oTalSyntax);
    }

    public function testPhpCodeWithSemicolon(): void
    {
        $this->assertFile('php-code-semicolon', $this->oTalSyntax);
    }

    public function testInlinePhpCode(): void
    {
        $this->assertFile('php-code-inline', $this->oTalSyntax);
    }

    public function testEcho(): void
    {
        $this->assertFile('echo', $this->oTalSyntax);
    }

    public function testIfStatement(): void
    {
        $this->assertFile('if', $this->oTalSyntax);
    }

    public function testElseifStatement(): void
    {
        $this->assertFile('elseif', $this->oTalSyntax);
    }

    public function testElseStatement(): void
    {
        $this->assertFile('else', $this->oTalSyntax);
    }

    public function testIfISet(): void
    {
        $this->assertFile('if-set', $this->oTalSyntax);
    }

    public function testIfEmpty(): void
    {
        $this->assertFile('if-empty', $this->oTalSyntax);
    }

    public function testIfEqual(): void
    {
        $this->assertFile('if-equal', $this->oTalSyntax);
    }

    public function testForLoop(): void
    {
        $this->assertFile('for', $this->oTalSyntax);
    }

    public function testWhileLoop(): void
    {
        $this->assertFile('while', $this->oTalSyntax);
    }

    public function testEachLoop(): void
    {
        $this->assertFile('each', $this->oTalSyntax);
    }

    public function testDesignModelObject(): void
    {
        $this->assertFile('design-model', $this->oTalSyntax);
    }

    public function testEscapeFunction(): void
    {
        $this->assertFile('escape', $this->oTalSyntax);
    }

    public function testInlineLangFunction(): void
    {
        $this->assertFile('lang-inline', $this->oTalSyntax);
    }

    public function testLangFunction(): void
    {
        $this->assertFile('lang', $this->oTalSyntax);
    }

    public function testLiteralFunction(): void
    {
        $this->assertFile('literal', $this->oTalSyntax);
    }

    public function testVariable(): void
    {
        $this->assertFile('variable', $this->oTalSyntax);
    }

    public function testObjectShortcuts(): void
    {
        $this->assertFile('shortcuts', $this->oTalSyntax);
    }

    public function testSingleLineComment(): void
    {
        $this->assertFile('comment-single-line', $this->oTalSyntax);
    }

    public function testComment(): void
    {
        $this->assertFile('comment', $this->oTalSyntax);
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
