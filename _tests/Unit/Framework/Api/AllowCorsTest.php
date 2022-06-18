<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\AllowCors;
use PHPUnit\Framework\TestCase;

final class AllowCorsTest extends TestCase
{
    public function testCorsHeader(): void
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Xdebug is required for this test. Please install it.');
        }

        $oCors = new AllowCors();
        $oCors->init();

        $aHeaders = xdebug_get_headers();
        $this->assertContains('Access-Control-Allow-Origin:*', $aHeaders);
        $this->assertContains('Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH, OPTIONS', $aHeaders);
    }

    protected function tearDown(): void
    {
        header_remove();
    }
}
