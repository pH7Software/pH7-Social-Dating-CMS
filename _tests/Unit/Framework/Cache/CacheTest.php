<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Cache
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Cache;

use PH7\Framework\Cache\Cache;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;

final class CacheTest extends TestCase
{
    private string $sCacheDir;

    protected function setUp(): void
    {
        $this->sCacheDir = sys_get_temp_dir() . '/ph7_cache_test_' . uniqid('', true) . DIRECTORY_SEPARATOR;
        mkdir($this->sCacheDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (!is_dir($this->sCacheDir)) {
            return;
        }

        $oIterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->sCacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($oIterator as $oPath) {
            $oPath->isDir() ? rmdir($oPath->getPathname()) : unlink($oPath->getPathname());
        }

        rmdir($this->sCacheDir);
    }

    public function testGetKeepsCachedStdClassUsable(): void
    {
        $oData = new stdClass;
        $oData->pageTitle = 'Home';

        $oCache = (new Cache)
            ->enabled(true)
            ->setCacheDir($this->sCacheDir)
            ->start('unit', 'stdclass', 3600);

        $oCache->put($oData);
        $oCachedData = $oCache->get();

        $this->assertInstanceOf(stdClass::class, $oCachedData);
        $this->assertSame('Home', $oCachedData->pageTitle);
    }
}
