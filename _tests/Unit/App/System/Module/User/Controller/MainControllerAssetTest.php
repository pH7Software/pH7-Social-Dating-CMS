<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\User\Controller;

use PHPUnit\Framework\TestCase;

final class MainControllerAssetTest extends TestCase
{
    public function testBackgroundVideoFlagRemainsBooleanWhenSelectingSplashStyles(): void
    {
        $sSource = file_get_contents(PH7_PATH_SYS_MOD . 'user/controllers/MainController.php');
        $this->assertIsString($sSource);

        $this->assertStringContainsString(
            'private function addGuestAssetFiles(bool $bIsBgVideo): void',
            $sSource
        );
        $this->assertStringContainsString("\$bIsBgVideo === true ? 'video_splash.css,' : ''", $sSource);
        $this->assertStringNotContainsString('string $bIsBgVideo', $sSource);
    }
}
