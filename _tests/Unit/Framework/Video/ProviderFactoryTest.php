<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Video
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Video;

use PH7\Framework\Video\Api\Dailymotion;
use PH7\Framework\Video\Api\Metacafe;
use PH7\Framework\Video\Api\Vimeo;
use PH7\Framework\Video\Api\Youtube;
use PH7\Framework\Video\InvalidApiProviderException;
use PH7\Framework\Video\ProviderFactory;
use PHPUnit\Framework\TestCase;

final class ProviderFactoryTest extends TestCase
{
    /**
     * @dataProvider videoApiProvider
     */
    public function testCreateValidApiProvider(string $sClassName, string $sExpectedClass): void
    {
        $oProvider = ProviderFactory::create($sClassName);
        $this->assertInstanceOf($sExpectedClass, $oProvider);
    }

    public function testCreateWrongApiProvider(): void
    {
        $this->expectException(InvalidApiProviderException::class);

        ProviderFactory::create('invalidVideoApiProvider');
    }

    public function videoApiProvider(): array
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
