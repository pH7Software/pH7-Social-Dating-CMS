<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Tools / Cli / Misc
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Tools\Cli\Misc;

use PH7\Cli\Misc\Helper;
use PHPUnit\Framework\TestCase;

final class HelperTest extends TestCase
{
    public function testCleanStringEscapesDoubleQuotes(): void
    {
        $this->assertSame('say \"hello\"', Helper::cleanString('say "hello"'));
    }

    public function testGenerateHashRespectsRequestedLength(): void
    {
        $this->assertSame(40, strlen(Helper::generateHash(40)));
    }
}
