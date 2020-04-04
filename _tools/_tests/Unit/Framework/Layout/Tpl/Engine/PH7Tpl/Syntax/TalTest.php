<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Tal as TalSyntax;

class TalTest extends SyntaxTestCase
{
    const INPUT_DIR = 'input/tal/';
    const OUTPUT_DIR = 'output/tal/';
    const INPUT_TPL_FILE_EXT = '.tal.tpl';
    const OUTPUT_PHP_FILE_EXT = '.tal.output';

    /** @var TalSyntax */
    private $oTalSyntax;

    protected function setUp()
    {
        parent::setUp();

        $this->oTalSyntax = new TalSyntax;
    }

    public function testParseUnsetCode()
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::TAL_SYNTAX);

        $this->oTalSyntax->parse();
    }

    public function testAutoIncludeStatement()
    {
        $this->assertFile('auto-include', $this->oTalSyntax);
    }

    public function testIncludeStatement()
    {
        $this->assertFile('include', $this->oTalSyntax);
    }

    public function testMainIncludeStatement()
    {
        $this->assertFile('main-include', $this->oTalSyntax);
    }

    public function testDefMainAutoIncludeStatement()
    {
        $this->assertFile('def-main-auto-include', $this->oTalSyntax);
    }

    public function testDefMainIncludeStatement()
    {
        $this->assertFile('def-main-include', $this->oTalSyntax);
    }

    public function testManualIncludeStatement()
    {
        $this->assertFile('manual-include', $this->oTalSyntax);
    }

    public function testPhpCode()
    {
        $this->assertFile('php-code', $this->oTalSyntax);
    }

    public function testPhpCodeWithSemicolon()
    {
        $this->assertFile('php-code-semicolon', $this->oTalSyntax);
    }

    public function testInlinePhpCode()
    {
        $this->assertFile('php-code-inline', $this->oTalSyntax);
    }

    public function testEcho()
    {
        $this->assertFile('echo', $this->oTalSyntax);
    }

    public function testIfStatement()
    {
        $this->assertFile('if', $this->oTalSyntax);
    }

    public function testElseifStatement()
    {
        $this->assertFile('elseif', $this->oTalSyntax);
    }

    public function testElseStatement()
    {
        $this->assertFile('else', $this->oTalSyntax);
    }

    public function testIfISet()
    {
        $this->assertFile('if-set', $this->oTalSyntax);
    }

    public function testIfEmpty()
    {
        $this->assertFile('if-empty', $this->oTalSyntax);
    }

    public function testIfEqual()
    {
        $this->assertFile('if-equal', $this->oTalSyntax);
    }

    public function testForLoop()
    {
        $this->assertFile('for', $this->oTalSyntax);
    }

    public function testWhileLoop()
    {
        $this->assertFile('while', $this->oTalSyntax);
    }

    public function testEachLoop()
    {
        $this->assertFile('each', $this->oTalSyntax);
    }

    public function testDesignModelObject()
    {
        $this->assertFile('design-model', $this->oTalSyntax);
    }

    public function testEscapeFunction()
    {
        $this->assertFile('escape', $this->oTalSyntax);
    }

    public function testInlineLangFunction()
    {
        $this->assertFile('lang-inline', $this->oTalSyntax);
    }

    public function testLangFunction()
    {
        $this->assertFile('lang', $this->oTalSyntax);
    }

    public function testLiteralFunction()
    {
        $this->assertFile('literal', $this->oTalSyntax);
    }

    public function testVariable()
    {
        $this->assertFile('variable', $this->oTalSyntax);
    }

    public function testObjectShortcuts()
    {
        $this->assertFile('shortcuts', $this->oTalSyntax);
    }

    public function testSingleLineComment()
    {
        $this->assertFile('comment-single-line', $this->oTalSyntax);
    }

    public function testComment()
    {
        $this->assertFile('comment', $this->oTalSyntax);
    }

    protected function getInputDirectory()
    {
        return self::INPUT_DIR;
    }

    protected function getOutputDirectory()
    {
        return self::OUTPUT_DIR;
    }

    protected function getInputTemplateFileExtension()
    {
        return self::INPUT_TPL_FILE_EXT;
    }

    protected function getOutputPhpFileExtension()
    {
        return self::OUTPUT_PHP_FILE_EXT;
    }
}
