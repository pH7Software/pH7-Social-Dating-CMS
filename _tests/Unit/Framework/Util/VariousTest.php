<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Util
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Util;

use PH7\Framework\Util\Various;
use PHPUnit\Framework\TestCase;

class VariousTest extends TestCase
{
    public function testGenerateRandom(): void
    {
        $iStringLength = strlen(Various::genRnd('Pierre-Henry Random :D', 8));
        $this->assertSame(8, $iStringLength);
    }

    public function testPaddingString(): void
    {
        $this->assertSame('abc def ghiabc def ghiabc def ghiabc def', Various::padStr('abc def ghi', 40));
    }

    public function testGenerateRandomWord(): void
    {
        $iStringLength = strlen(Various::genRndWord(10));
        $this->assertSame(10, $iStringLength);
    }
}
