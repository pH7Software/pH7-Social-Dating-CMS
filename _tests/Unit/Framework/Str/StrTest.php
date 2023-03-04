<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Str
 */

namespace PH7\Test\Unit\Framework\Str;

use PH7\Framework\Str\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    private Str $oStr;

    protected function setUp(): void
    {
        $this->oStr = new Str;
    }

    public function testLower(): void
    {
        $sOutputString = $this->oStr->lower('Hello The WORLD');
        $this->assertSame('hello the world', $sOutputString);
    }

    public function testUpper(): void
    {
        $sOutputString = $this->oStr->upper('Hello The world');
        $this->assertSame('HELLO THE WORLD', $sOutputString);
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->oStr->equals('Hi You', 'Hi You'));
    }

    public function testNotEquals(): void
    {
        $this->assertFalse($this->oStr->equals(1, '1'));
    }

    public function testEqualsIgnoreCase(): void
    {
        $this->assertTrue($this->oStr->equalsIgnoreCase('hI YoU', 'Hi YOU'));
    }

    public function testNotEqualsIgnoreCase(): void
    {
        $this->assertFalse($this->oStr->equalsIgnoreCase('hI YoU!', 'Hi YOU'));
    }

    public function testNotNoSpaces(): void
    {
        $this->assertFalse(Str::noSpaces('   '));
    }

    public function testEscape(): void
    {
        $this->assertSame('&lt;b&gt;Me &amp; You&lt;/b&gt;', $this->oStr->escape('<b>Me & You</b>'));
        $this->assertSame('Me & You', $this->oStr->escape('<b>Me & You</b>', true));
    }
}
