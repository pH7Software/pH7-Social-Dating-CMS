<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/Module.php';

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Module;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ModulePathTest extends TestCase
{
    public function testValidModuleFolderIsAccepted(): void
    {
        $this->assertTrue(Module::isValidModuleFolder('my-module2'));
    }

    #[DataProvider('unsafeModuleFolderProvider')]
    public function testUnsafeModuleFolderIsRejected(mixed $mFolder): void
    {
        $this->assertFalse(Module::isValidModuleFolder($mFolder));
    }

    public static function unsafeModuleFolderProvider(): array
    {
        return [
            'parent traversal' => ['aa/../../outside'],
            'Windows traversal' => ['aa\\..\\outside'],
            'absolute path' => ['/tmp/module'],
            'valid prefix with suffix' => ['valid-module.php'],
            'too long' => [str_repeat('a', 36)],
            'array' => [['module']]
        ];
    }

    public function testSetPathRejectsUnvalidatedCallers(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        (new Module())->setPath('../outside');
    }
}
