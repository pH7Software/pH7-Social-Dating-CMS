<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class PH7TplTest extends TestCase
{
    public function testMainTemplateCompilePathContainsFilenameOnce(): void
    {
        $oMethod = new ReflectionMethod(PH7Tpl::class, 'buildCompileFilePath');
        $sCompileDir = PH7_PATH_CACHE . 'pH7tpl_compile/public_main/base/';

        $this->assertSame(
            $sCompileDir . 'layout.cpl.php',
            $oMethod->invoke(null, $sCompileDir, true, 'layout', 'main')
        );
    }

    public function testModuleTemplateCompilePathRemovesControllerPrefix(): void
    {
        $oMethod = new ReflectionMethod(PH7Tpl::class, 'buildCompileFilePath');
        $sCompileDir = PH7_PATH_CACHE . 'pH7tpl_compile/user/base/main/';

        $this->assertSame(
            $sCompileDir . 'index.cpl.php',
            $oMethod->invoke(null, $sCompileDir, false, 'mainindex', 'main')
        );
    }
}
