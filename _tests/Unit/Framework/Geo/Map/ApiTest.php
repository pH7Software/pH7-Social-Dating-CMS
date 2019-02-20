<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Geo / Map
 */

namespace PH7\Test\Unit\Framework\Geo\Map;

use PH7\Framework\Geo\Map\Api as ApiMap;
use PHPUnit_Framework_TestCase;

class ApiTest extends PHPUnit_Framework_TestCase
{
    public function testApiKeyIsSet()
    {
        $oMap = new ApiMap;
        $oMap->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertFalse($oMap->isApiKeyNotSet());
    }

    public function testWrongApiKeySet()
    {
        $oMap = new ApiMap;
        $oMap->setKey('invalid');
        $this->assertTrue($oMap->isApiKeyNotSet());
    }

    public function testApiKeyIsNotSet()
    {
        $oMap = new ApiMap;
        $this->assertTrue($oMap->isApiKeyNotSet());
    }
}
