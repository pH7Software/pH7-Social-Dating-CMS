<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Geo / Map
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Geo\Map;

use PH7\Framework\Geo\Map\Api as ApiMap;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testApiKeyIsSet(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertFalse($oMap->isApiKeyNotSet());
    }

    public function testWrongApiKeySet(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey('invalid');
        $this->assertTrue($oMap->isApiKeyNotSet());
    }

    public function testApiKeyIsNotSet(): void
    {
        $oMap = new ApiMap;
        $this->assertTrue($oMap->isApiKeyNotSet());
    }
}
