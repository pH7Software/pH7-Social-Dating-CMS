<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class ModuleFeatureGateTest extends TestCase
{
    private const CORE_MODULES = [
        'admin123',
        'api',
        'comment',
        'contact',
        'error',
        'field',
        'game',
        'lost-password',
        'page',
        'payment',
        'ph7cms-helper',
        'report',
        'two-factor-auth',
        'user',
        'xml'
    ];

    public function testEveryModuleIsExplicitlyCoreOrFeatureGated(): void
    {
        $sRootPath = dirname(__DIR__, 3);
        $aModulePaths = glob($sRootPath . '/_protected/app/system/modules/*', GLOB_ONLYDIR);
        $sInstallSql = file_get_contents($sRootPath . '/_install/data/sql/MySQL/pH7_Core.sql');

        $this->assertIsArray($aModulePaths);
        $this->assertIsString($sInstallSql);

        $aModules = array_map('basename', $aModulePaths);
        preg_match_all(
            "/^\(.*?, '([^']+)', '[01]', '[01]'\)[,;]?$/m",
            $sInstallSql,
            $aFeatureGateMatches
        );
        $aFeatureGatedModules = $aFeatureGateMatches[1];
        $aClassifiedModules = array_merge(self::CORE_MODULES, $aFeatureGatedModules);

        sort($aModules);
        sort($aClassifiedModules);

        $this->assertSame([], array_intersect(self::CORE_MODULES, $aFeatureGatedModules));
        $this->assertSame($aModules, $aClassifiedModules);
    }
}
