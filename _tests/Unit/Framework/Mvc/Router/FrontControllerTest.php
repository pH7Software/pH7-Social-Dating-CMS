<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc / Router
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Router;

use PH7\Framework\Mvc\Router\FrontController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class FrontControllerTest extends TestCase
{
    public function testActionNormalizationAcceptsMissingAjaxActions(): void
    {
        $oMethod = (new ReflectionClass(FrontController::class))->getMethod('normalizeAction');

        $this->assertSame('', $oMethod->invoke(null, null));
        $this->assertSame('showprofile', $oMethod->invoke(null, 'ShowProfile'));
    }
}
