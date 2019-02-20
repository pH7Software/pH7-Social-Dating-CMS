<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Video
 */

namespace PH7\Test\Unit\Framework\Video;

use PH7\Framework\Video\Api\Dailymotion;
use PH7\Framework\Video\Api\Metacafe;
use PH7\Framework\Video\Api\Vimeo;
use PH7\Framework\Video\Api\Youtube;
use PH7\Framework\Video\InvalidApiProviderException;
use PH7\Framework\Video\ProviderFactory;
use PHPUnit_Framework_TestCase;

class ProviderFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sClassName
     * @param string $sExpectedClass
     *
     * @dataProvider videoApiProvider
     */
    public function testCreateValidApiProvider($sClassName, $sExpectedClass)
    {
        $oProvider = ProviderFactory::create($sClassName);
        $this->assertInstanceOf($sExpectedClass, $oProvider);
    }

    public function testCreateWrongApiProvider()
    {
        $this->expectException(InvalidApiProviderException::class);

        ProviderFactory::create('invalidVideoApiProvider');
    }

    /**
     * @return array
     */
    public function videoApiProvider()
    {
        return [
            ['youtu', Youtube::class],
            ['youtube', Youtube::class],
            ['dailymotion', Dailymotion::class],
            ['dai', Dailymotion::class],
            ['vimeo', Vimeo::class],
            ['metacafe', Metacafe::class],
        ];
    }
}
