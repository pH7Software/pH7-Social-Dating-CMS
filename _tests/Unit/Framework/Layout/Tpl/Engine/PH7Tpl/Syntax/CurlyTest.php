<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
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
