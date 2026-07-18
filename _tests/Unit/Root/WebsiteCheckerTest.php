<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

require_once dirname(__DIR__, 3) . '/WebsiteChecker.php';

use PH7\WebsiteChecker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class WebsiteCheckerTest extends TestCase
{
    public function testCheckPhpVersionDoesNotThrowOnSupportedRuntime(): void
    {
        $oChecker = new WebsiteChecker;
        $oChecker->checkPhpVersion();

        $this->addToAssertionCount(1);
    }

    public function testInstallFolderExistsInProjectRoot(): void
    {
        $this->assertTrue((new WebsiteChecker)->doesInstallFolderExist());
    }

    public function testNoConfigFoundMessageIsStable(): void
    {
        $this->assertSame(
            'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.',
            (new WebsiteChecker)->getNoConfigFoundMessage()
        );
    }

    public function testIncompatiblePhpVersionFlagIsFalseOnCurrentRuntime(): void
    {
        $oChecker = new WebsiteChecker;
        $oReflection = new ReflectionClass(WebsiteChecker::class);
        $oMethod = $oReflection->getMethod('isIncompatiblePhpVersion');

        $this->assertFalse($oMethod->invoke($oChecker));
    }
}
