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
        $sFirstValue = Various::genRnd('Pierre-Henry Random :D', 40);
        $sSecondValue = Various::genRnd('Pierre-Henry Random :D', 40);

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
        $this->assertStringNotContainsString('catch (', $sSource);
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
