<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use Xefi\Faker\Container\Container;
use Xefi\Faker\DeDe\FakerDeDeServiceProvider;
use Xefi\Faker\EnGb\FakerEnGbServiceProvider;
use Xefi\Faker\EnUs\FakerEnUsServiceProvider;
use Xefi\Faker\EsEs\FakerEsEsServiceProvider;
use Xefi\Faker\FakerServiceProvider;
use Xefi\Faker\FrBe\FakerFrBeServiceProvider;
use Xefi\Faker\FrCh\FakerFrChServiceProvider;
use Xefi\Faker\FrFr\FakerFrFrServiceProvider;

final class FakerContainerFactory
{
    private static bool $bProvidersRegistered = false;

    public static function create(string $sLocale): Container
    {
        self::registerProviders();

        $oFaker = new class extends Container {
            public function __construct()
            {
                if (!$this->areExtensionsInitialized()) {
                    $this->bootstrap();
                }
            }
        };

        return $oFaker->locale($sLocale);
    }

    private static function registerProviders(): void
    {
        if (self::$bProvidersRegistered) {
            return;
        }

        (new FakerServiceProvider())->boot();
        (new FakerEnUsServiceProvider())->boot();
        (new FakerEnGbServiceProvider())->boot();
        (new FakerFrFrServiceProvider())->boot();
        (new FakerFrBeServiceProvider())->boot();
        (new FakerFrChServiceProvider())->boot();
        (new FakerDeDeServiceProvider())->boot();
        (new FakerEsEsServiceProvider())->boot();

        self::$bProvidersRegistered = true;
    }
}
