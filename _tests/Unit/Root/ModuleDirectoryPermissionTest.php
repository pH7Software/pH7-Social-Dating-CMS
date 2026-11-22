<?php
/**
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class ModuleDirectoryPermissionTest extends TestCase
{
    public function testInstallingAndUninstallingModulesCannotCreateWorldWritableDirectories(): void
    {
        $sSource = file_get_contents(
            dirname(PH7_PATH_PROTECTED) . '/_protected/app/system/modules/admin123/inc/class/Module.php'
        );

        $this->assertIsString($sSource);
        $this->assertSame(2, substr_count($sSource, ', 0775);'));
        $this->assertStringNotContainsString('Chmod::MODE_ALL_EXEC', $sSource);
    }
}
