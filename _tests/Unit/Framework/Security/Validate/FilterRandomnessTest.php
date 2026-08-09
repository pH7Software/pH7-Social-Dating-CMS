<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security\Validate;

use PHPUnit\Framework\TestCase;

final class FilterRandomnessTest extends TestCase
{
    public function testLegacySecurityHashesUseTheSystemCSPRNG(): void
    {
        $sSource = file_get_contents(PH7_PATH_FRAMEWORK . 'Security/Validate/Filter.class.php');

        $this->assertIsString($sSource);
        $this->assertSame(2, substr_count($sSource, 'bin2hex(random_bytes(16))'));
        $this->assertStringNotContainsString('mt_rand(', $sSource);
        $this->assertStringNotContainsString('uniqid(', $sSource);
    }

    public function testLegacyFilterAlsoRejectsInlineStyleAttributes(): void
    {
        $sSource = file_get_contents(PH7_PATH_FRAMEWORK . 'Security/Validate/Filter.class.php');

        $this->assertIsString($sSource);
        $this->assertStringContainsString("['on\\w*', 'style', 'xmlns', 'formaction']", $sSource);
    }
}
