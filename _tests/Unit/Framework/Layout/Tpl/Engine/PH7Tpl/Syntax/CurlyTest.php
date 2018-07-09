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
use PHPUnit_Framework_TestCase;

class CurlyTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_PATH = __DIR__  . '/fixtures/';
    const INPUT_DIR = 'input/curly/';
    const OUTPUT_DIR = 'output/curly/';
    const INPUT_TPL_FILE_EXT = '.curly.tpl';
    const OUTPUT_PHP_FILE_EXT = '.curly.output';

    /** @var CurlySyntax */
    private $oCurlySyntax;

    protected function setUp()
    {
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
        $this->assertFile('php-code');
    }

    public function testEcho()
    {
        $this->assertFile('echo');
    }

    public function testIfStatement()
    {
        $this->assertFile('if');
    }

    public function testElseifStatement()
    {
        $this->assertFile('elseif');
    }

    public function testElseStatement()
    {
        $this->assertFile('else');
    }

    public function testForLoop()
    {
        $this->assertFile('for');
    }

    public function testWhileLoop()
    {
        $this->assertFile('while');
    }

    public function testEachLoop()
    {
        $this->assertFile('each');
    }

    public function testEscapeFunction()
    {
        $this->assertFile('escape');
    }

    public function testInlineLangFunction()
    {
        $this->assertFile('lang-inline');
    }

    public function testLangFunction()
    {
        $this->assertFile('lang');
    }

    public function testLiteralFunction()
    {
        $this->assertFile('literal');
    }

    private function assertFile($sName)
    {
        $sTplCode = file_get_contents(self::FIXTURE_PATH . self::INPUT_DIR . $sName . self::INPUT_TPL_FILE_EXT);
        $sPhpCode = file_get_contents(self::FIXTURE_PATH  . self::OUTPUT_DIR . $sName . self::OUTPUT_PHP_FILE_EXT);
        $this->oCurlySyntax->set($sTplCode);
        $this->oCurlySyntax->parse();

        $this->assertSame($sPhpCode, $this->oCurlySyntax->get());
    }
}
