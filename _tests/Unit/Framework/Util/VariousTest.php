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
        $sFirstValue = Various::genRnd('Pierre-Henry_Seven_Soria', 40);
        $sSecondValue = Various::genRnd('Pierre-Henry_Seven_Soria', 40);

        $this->assertSame(40, strlen($sFirstValue));
        $this->assertMatchesRegularExpression('/\A[a-f0-9]{40}\z/', $sFirstValue);
        $this->assertNotSame($sFirstValue, $sSecondValue);
    }

    public function testRandomGenerationHasNoWeakFallback(): void
    {
        $sSource = file_get_contents(PH7_PATH_FRAMEWORK . 'Util/Various.class.php');

        $this->assertIsString($sSource);
        $this->assertStringContainsString('random_bytes(', $sSource);
        $this->assertStringNotContainsString('mt_rand(', $sSource);
        $this->assertStringNotContainsString('uniqid(', $sSource);
        $this->assertStringNotContainsString('array_rand(', $sSource);
        $this->assertStringNotContainsString('catch (', $sSource);
    }

    public function testNonPositiveRandomLengthsReturnEmptyStrings(): void
    {
        $this->assertSame('', Various::genRnd(null, 0));
        $this->assertSame('', Various::genRndWord(-1));
    }

    public function testPaddingString(): void
    {
        $this->assertSame('abc def ghiabc def ghiabc def ghiabc def', Various::padStr('abc def ghi', 40));
    }

    public function testGenerateRandomWord(): void
    {
        $sWord = Various::genRndWord(64);

        $this->assertSame(64, strlen($sWord));
        $this->assertMatchesRegularExpression('/\A[0-9a-zA-Z_~|%^!$#@?\-]{64}\z/', $sWord);
    }
}
