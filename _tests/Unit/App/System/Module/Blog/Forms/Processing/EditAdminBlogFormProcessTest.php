<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Blog\Forms\Processing;

use PHPUnit\Framework\TestCase;

final class EditAdminBlogFormProcessTest extends TestCase
{
    public function testModificationDateUsesTheRowIdRatherThanTheUrlSlug(): void
    {
        $sProcess = file_get_contents(
            PH7_PATH_SYS_MOD . 'blog/forms/processing/EditAdminBlogFormProcess.php'
        );

        self::assertIsString($sProcess);
        self::assertMatchesRegularExpression(
            '/->updatePost\(\s*\x27updatedDate\x27,\s*\$this->dateTime->get\(\)->dateTime\([^)]*\),\s*\$iBlogId\s*\)/',
            $sProcess
        );
    }
}
