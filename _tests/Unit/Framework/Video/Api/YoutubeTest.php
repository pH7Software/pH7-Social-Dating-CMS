<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Video / Api
 */

namespace PH7\Test\Unit\Framework\Video\Api;

use PH7\Framework\Video\Api\Youtube;
use PHPUnit_Framework_TestCase;

class YoutubeTest extends PHPUnit_Framework_TestCase
{
    public function testApiKeyIsSet()
    {
        $oYoutube = new Youtube;
        $oYoutube->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertTrue($oYoutube->isApiKeySet());
    }

    public function testWrongApiKeySet()
    {
        $oYoutube = new Youtube;
        $oYoutube->setKey('invalid_key');
        $this->assertFalse($oYoutube->isApiKeySet());
    }

    public function testApiKeyIsNotSet()
    {
        $oYoutube = new Youtube;
        $this->assertFalse($oYoutube->isApiKeySet());
    }
}
