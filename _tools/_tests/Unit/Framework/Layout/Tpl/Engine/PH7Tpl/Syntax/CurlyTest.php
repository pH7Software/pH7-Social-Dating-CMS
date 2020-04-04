<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly as CurlySyntax;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;

class CurlyTest extends SyntaxTestCase
{
    const INPUT_DIR = 'input/curly/';
    const OUTPUT_DIR = 'output/curly/';
    const INPUT_TPL_FILE_EXT = '.curly.tpl';
    const OUTPUT_PHP_FILE_EXT = '.curly.output';

    /** @var CurlySyntax */
    private $oCurlySyntax;

    protected function setUp()
    {
        parent::setUp();

        $this->oCurlySyntax = new CurlySyntax;
    }

    public function testParseUnsetCode()
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::CURLY_SYNTAX);

        $this->oCurlySyntax->parse();
    }

    public function testAutoIncludeStatement()
    {
        $this->assertFile('auto-include', $this->oCurlySyntax);
    }

    public function testIncludeStatement()
    {
        $this->assertFile('include', $this->oCurlySyntax);
    }

    public function testMainIncludeStatement()
    {
        $this->assertFile('main-include', $this->oCurlySyntax);
    }

    public function testDefMainAutoIncludeStatement()
    {
        $this->assertFile('def-main-auto-include', $this->oCurlySyntax);
    }

    public function testDefMainIncludeStatement()
    {
        $this->assertFile('def-main-include', $this->oCurlySyntax);
    }

    public function testManualIncludeStatement()
    {
        $this->assertFile('manual-include', $this->oCurlySyntax);
    }

    public function testPhpCode()
    {
        $this->assertFile('php-code', $this->oCurlySyntax);
    }

    public function testPhpCodeWithSemicolon()
    {
        $this->assertFile('php-code-semicolon', $this->oCurlySyntax);
    }

    public function testEcho()
    {
        $this->assertFile('echo', $this->oCurlySyntax);
    }

    public function testEchoWithSemicolon()
    {
        $this->assertFile('echo-semicolon', $this->oCurlySyntax);
    }

    public function testIfStatement()
    {
        $this->assertFile('if', $this->oCurlySyntax);
    }

    public function testElseifStatement()
    {
        $this->assertFile('elseif', $this->oCurlySyntax);
    }

    public function testElseStatement()
    {
        $this->assertFile('else', $this->oCurlySyntax);
    }

    public function testForLoop()
    {
        $this->assertFile('for', $this->oCurlySyntax);
    }

    public function testWhileLoop()
    {
        $this->assertFile('while', $this->oCurlySyntax);
    }

    public function testEachLoop()
    {
        $this->assertFile('each', $this->oCurlySyntax);
    }

    public function testDesignModelObject()
    {
        $this->assertFile('design-model', $this->oCurlySyntax);
    }

    public function testEscapeFunction()
    {
        $this->assertFile('escape', $this->oCurlySyntax);
    }

    public function testInlineLangFunction()
    {
        $this->assertFile('lang-inline', $this->oCurlySyntax);
    }

    public function testLangFunction()
    {
        $this->assertFile('lang', $this->oCurlySyntax);
    }

    public function testLiteralFunction()
    {
        $this->assertFile('literal', $this->oCurlySyntax);
    }

    public function testVariable()
    {
        $this->assertFile('variable', $this->oCurlySyntax);
    }

    public function testObjectShortcuts()
    {
        $this->assertFile('shortcuts', $this->oCurlySyntax);
    }

    public function testSingleLineComment()
    {
        $this->assertFile('comment-single-line', $this->oCurlySyntax);
    }

    public function testComment()
    {
        $this->assertFile('comment', $this->oCurlySyntax);
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
