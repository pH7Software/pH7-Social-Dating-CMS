<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Models;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RegistrationFailureBoundaryTest extends TestCase
{
    #[DataProvider('registrationProcessProvider')]
    public function testInteractiveRegistrationFailuresAreLoggedWithoutRawUiErrors(
        string $sPath,
        string $sLogPrefix,
        string $sUserMessage
    ): void {
        $sProcess = file_get_contents(PH7_PATH_SYS_MOD . $sPath);

        $this->assertIsString($sProcess);
        $this->assertStringContainsString('catch (\\Throwable $oException)', $sProcess);
        $this->assertStringContainsString($sLogPrefix, $sProcess);
        $this->assertStringContainsString($sUserMessage, $sProcess);
        $this->assertStringNotContainsString('setError($oException->getMessage()', $sProcess);
    }

    public static function registrationProcessProvider(): array
    {
        return [
            'member signup' => [
                'user/forms/processing/JoinFormProcess.php',
                'Member registration failed:',
                'An error occurred during registration!'
            ],
            'affiliate signup' => [
                'affiliate/forms/processing/JoinFormProcess.php',
                'Affiliate registration failed:',
                'An error occurred during registration!'
            ],
            'admin member' => [
                'admin123/forms/processing/AddUserFormProcess.php',
                'Admin member creation failed:',
                'The user could not be added.'
            ],
            'admin affiliate' => [
                'affiliate/forms/processing/AddAffiliateFormProcess.php',
                'Admin affiliate creation failed:',
                'The affiliate could not be added.'
            ]
        ];
    }
}
