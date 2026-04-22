<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Tools / Cli / Misc
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Tools\Cli\Misc;

use PH7\Cli\Misc\Validation;
use PHPUnit\Framework\TestCase;

final class ValidationTest extends TestCase
{
    public function testValidEmail(): void
    {
        $this->assertTrue((new Validation('admin@example.com'))->isValidEmail());
    }

    public function testInvalidEmail(): void
    {
        $this->assertFalse((new Validation('not-an-email'))->isValidEmail());
    }

    public function testValidName(): void
    {
        $this->assertTrue((new Validation('Pierre'))->isValidName());
    }

    public function testInvalidNameTooShort(): void
    {
        $this->assertFalse((new Validation('A'))->isValidName());
    }
}
