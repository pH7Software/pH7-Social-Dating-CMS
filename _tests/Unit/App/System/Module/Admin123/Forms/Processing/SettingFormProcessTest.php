<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / App / System / Module / Admin123 / Forms / Processing
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Forms\Processing;

require_once PH7_PATH_SYS_MOD . 'admin123/forms/processing/SettingFormProcess.php';

use PH7\SettingFormProcess;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class SettingFormProcessTest extends TestCase
{
    public function testBlankCronSecretPreservesExistingValue(): void
    {
        $oMethod = new ReflectionMethod(SettingFormProcess::class, 'shouldPreserveExistingSecret');

        $this->assertTrue($oMethod->invoke(null, 'cron_security_hash', ''));
        $this->assertFalse($oMethod->invoke(null, 'cron_security_hash', 'replacement'));
        $this->assertFalse($oMethod->invoke(null, 'site_name', ''));
    }
}
