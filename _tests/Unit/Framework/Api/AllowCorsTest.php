<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\AllowCors;
use PHPUnit_Framework_TestCase;

class AllowCorsTest extends PHPUnit_Framework_TestCase
{
    public function testCorsHeader()
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

    protected function tearDown()
    {
        header_remove();
    }
}
