<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use Xefi\Faker\Container\Container;
use Xefi\Faker\Faker;

final class FakerGeneratorFactory
{
    private static bool $bConfigured = false;

    public static function create(string $sLocale): Faker
    {
        self::configure();

        return new Faker($sLocale);
    }

    private static function configure(): void
    {
        if (self::$bConfigured) {
            return;
        }

        Container::basePath(rtrim(PH7_PATH_PROTECTED, '/\\'));
        Container::packageManifestPath(PH7_PATH_CACHE . 'xefi-faker-packages.php');
        Container::containerMixinManifestPath(PH7_PATH_CACHE . 'xefi-faker-mixin.php');

        self::$bConfigured = true;
    }
}
