<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Video / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Video\Api;

use PH7\Framework\Video\Api\Youtube;
use PHPUnit\Framework\TestCase;

final class YoutubeTest extends TestCase
{
    private Youtube $oYoutube;

    protected function setUp(): void
    {
        $this->oYoutube = new Youtube;
    }

    public function testApiKeyIsSet(): void
    {
        $this->oYoutube->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertTrue($this->oYoutube->isApiKeySet());
    }

    public function testWrongApiKeySet(): void
    {
        $this->oYoutube->setKey('invalid');
        $this->assertFalse($this->oYoutube->isApiKeySet());
    }

    public function testApiKeyIsNotSet(): void
    {
        $this->assertFalse($this->oYoutube->isApiKeySet());
    }
}
