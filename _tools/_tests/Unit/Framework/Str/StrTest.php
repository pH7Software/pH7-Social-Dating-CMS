<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Str
 */

namespace PH7\Test\Unit\Framework\Str;

use PH7\Framework\Str\Str;
use PHPUnit_Framework_TestCase;

class StrTest extends PHPUnit_Framework_TestCase
{
    /** @var Str */
    private $oStr;

    protected function setUp()
    {
        $this->oStr = new Str;
    }

    public function testLower()
    {
        $sOutputString = $this->oStr->lower('Hello The WORLD');
        $this->assertSame('hello the world', $sOutputString);
    }

    public function testUpper()
    {
        $sOutputString = $this->oStr->upper('Hello The world');
        $this->assertSame('HELLO THE WORLD', $sOutputString);
    }

    public function testEquals()
    {
        $this->assertTrue($this->oStr->equals('Hi You', 'Hi You'));
    }

    public function testNotEquals()
    {
        $this->assertFalse($this->oStr->equals(1, '1'));
    }

    public function testEqualsIgnoreCase()
    {
        $this->assertTrue($this->oStr->equalsIgnoreCase('hI YoU', 'Hi YOU'));
    }

    public function testNotEqualsIgnoreCase()
    {
        $this->assertFalse($this->oStr->equalsIgnoreCase('hI YoU!', 'Hi YOU'));
    }

    public function testNotNoSpaces()
    {
        $this->assertFalse(Str::noSpaces('   '));
    }

    public function testEscape()
    {
        $this->assertSame('&lt;b&gt;Me &amp; You&lt;/b&gt;', $this->oStr->escape('<b>Me & You</b>'));
        $this->assertSame('Me & You', $this->oStr->escape('<b>Me & You</b>', true));
    }
}
