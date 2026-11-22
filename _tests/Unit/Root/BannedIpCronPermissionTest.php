<?php
/**
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class BannedIpCronPermissionTest extends TestCase
{
    public function testDownloadedBanListNeverBecomesWorldWritable(): void
    {
        // This cron file executes when included, so verify its permission policy without loading it.
        $sCron = file_get_contents(
            dirname(PH7_PATH_PROTECTED) . '/_protected/app/system/core/assets/cron/96h/BannedIpCoreCron.php'
        );

        $this->assertIsString($sCron);
        $this->assertStringContainsString(
            '$this->file->chmod(self::BANNED_IP_FILE_PATH, 0660);',
            $sCron
        );
        $this->assertStringNotContainsString('Chmod::MODE_ALL_EXEC', $sCron);
    }
}
