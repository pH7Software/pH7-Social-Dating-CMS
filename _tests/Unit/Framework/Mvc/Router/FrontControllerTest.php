<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc / Router
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Router;

use ErrorException;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Router\FrontController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class FrontControllerTest extends TestCase
{
    /**
     * The exception logger re-runs the database initialisation after the controller
     * has scrubbed the credentials from the config; that must not read them again.
     */
    public function testDatabaseInitialisationIsSkippedOnceCredentialsAreRemoved(): void
    {
        $oConfig = (new ReflectionClass(Config::class))->newInstanceWithoutConstructor();
        $oConfig->values = ['application' => []];
        $oReflection = new ReflectionClass(FrontController::class);
        $oFrontController = $oReflection->newInstanceWithoutConstructor();
        $oReflection->getProperty('oConfig')->setValue($oFrontController, $oConfig);

        set_error_handler(static function (int $iSeverity, string $sMessage): bool {
            throw new ErrorException($sMessage, 0, $iSeverity);
        });

        try {
            $oFrontController->_initializeDatabase();
            $this->addToAssertionCount(1);
        } finally {
            restore_error_handler();
        }
    }

    public function testActionNormalizationAcceptsMissingAjaxActions(): void
    {
        $oMethod = (new ReflectionClass(FrontController::class))->getMethod('normalizeAction');

        $this->assertSame('', $oMethod->invoke(null, null));
        $this->assertSame('showprofile', $oMethod->invoke(null, 'ShowProfile'));
    }
}
