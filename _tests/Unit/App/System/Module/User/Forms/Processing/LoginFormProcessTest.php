<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\User\Forms\Processing;

use PHPUnit\Framework\TestCase;

final class LoginFormProcessTest extends TestCase
{
    public function testMissingGeoLocationDoesNotBlockLogin(): void
    {
        $sSource = file_get_contents(
            PH7_PATH_SYS_MOD . 'user/forms/processing/LoginFormProcess.php'
        );

        $this->assertIsString($sSource);
        $this->assertStringContainsString(
            'function isForeignLocation(int $iProfileId, ?string $sLocationName): bool',
            $sSource
        );
        $this->assertStringContainsString("\$sLocationName === null || \$sLocationName === ''", $sSource);
    }
}
