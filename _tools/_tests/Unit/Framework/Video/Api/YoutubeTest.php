<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Video / Api
 */

namespace PH7\Test\Unit\Framework\Video\Api;

use PH7\Framework\Video\Api\Youtube;
use PHPUnit_Framework_TestCase;

class YoutubeTest extends PHPUnit_Framework_TestCase
{
    /** @var Youtube */
    private $oYoutube;

    protected function setUp()
    {
        $this->oYoutube = new Youtube;
    }

    public function testApiKeyIsSet()
    {
        $this->oYoutube->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertTrue($this->oYoutube->isApiKeySet());
    }

    public function testWrongApiKeySet()
    {
        $this->oYoutube->setKey('invalid');
        $this->assertFalse($this->oYoutube->isApiKeySet());
    }

    public function testApiKeyIsNotSet()
    {
        $this->assertFalse($this->oYoutube->isApiKeySet());
    }
}
