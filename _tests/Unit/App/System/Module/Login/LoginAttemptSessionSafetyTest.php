<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Login
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Login;

use PHPUnit\Framework\TestCase;

final class LoginAttemptSessionSafetyTest extends TestCase
{
    public function testSessionResetNeverClearsLoginAttemptCounters(): void
    {
        $aFiles = [
            PH7_PATH_SYS_MOD . 'user/forms/LoginForm.php',
            PH7_PATH_SYS_MOD . 'affiliate/forms/LoginForm.php',
            PH7_PATH_SYS_MOD . 'admin123/forms/LoginForm.php'
        ];

        foreach ($aFiles as $sFile) {
            $sSource = file_get_contents($sFile);
            $this->assertIsString($sSource);
            $this->assertStringContainsString('destroy', strtolower($sSource));
            $this->assertStringNotContainsString('clearLoginAttempts', $sSource);
            $this->assertStringNotContainsString('new SecurityModel', $sSource);
        }
    }
}
