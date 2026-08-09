<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

use PHPUnit\Framework\TestCase;

final class UserCoreUploadPathTest extends TestCase
{
    private string $sUserCore;

    protected function setUp(): void
    {
        $sUserCore = file_get_contents(dirname(__DIR__, 6) . '/_protected/app/system/core/classes/UserCore.php');

        $this->assertIsString($sUserCore);
        $this->sUserCore = $sUserCore;
    }

    public function testAvatarUsernameIsContainedBeforeAnyImageOrExistingAvatarMutation(): void
    {
        $sMethod = $this->getMethodSource('setAvatar', 'deleteAvatar');

        $iGuard = strpos($sMethod, "if (\$sSafeUsername === '' || \$sSafeUsername !== \$sUsername)");
        $iDecode = strpos($sMethod, 'new FileStorageImage(');
        $iDelete = strpos($sMethod, '$this->deleteAvatar($iProfileId, $sSafeUsername)');

        $this->assertIsInt($iGuard);
        $this->assertIsInt($iDecode);
        $this->assertIsInt($iDelete);
        $this->assertLessThan($iDecode, $iGuard);
        $this->assertLessThan($iDelete, $iGuard);
        $this->assertStringContainsString("'user/avatar/img/' . \$sSafeUsername", $sMethod);
    }

    public function testBackgroundUsernameIsContainedBeforeAnyImageOrExistingBackgroundMutation(): void
    {
        $sMethod = $this->getMethodSource('setBackground', 'deleteBackground');

        $iGuard = strpos($sMethod, "if (\$sSafeUsername === '' || \$sSafeUsername !== \$sUsername)");
        $iDecode = strpos($sMethod, 'new FileStorageImage(');
        $iDelete = strpos($sMethod, '$this->deleteBackground($iProfileId, $sSafeUsername)');

        $this->assertIsInt($iGuard);
        $this->assertIsInt($iDecode);
        $this->assertIsInt($iDelete);
        $this->assertLessThan($iDecode, $iGuard);
        $this->assertLessThan($iDelete, $iGuard);
        $this->assertStringContainsString("'user/background/img/' . \$sSafeUsername", $sMethod);
    }

    private function getMethodSource(string $sMethod, string $sNextMethod): string
    {
        $iStart = strpos($this->sUserCore, 'public function ' . $sMethod . '(');
        $iEnd = strpos($this->sUserCore, 'public function ' . $sNextMethod . '(', $iStart);

        $this->assertIsInt($iStart);
        $this->assertIsInt($iEnd);

        return substr($this->sUserCore, $iStart, $iEnd - $iStart);
    }
}
