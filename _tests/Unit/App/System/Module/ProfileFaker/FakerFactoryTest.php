<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Profile Faker
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\ProfileFaker;

use InvalidArgumentException;
use PH7\FakerFactory;
use PH7\FakerGeneratorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_SYS_MOD . 'profile-faker/inc/class/FakerFactory.php';
require_once PH7_PATH_APP . 'includes/classes/FakerGeneratorFactory.php';

final class FakerFactoryTest extends TestCase
{
    #[DataProvider('supportedLocaleProvider')]
    public function testXefiFakerGeneratesProfileDataForEverySupportedLocale(string $sLocale): void
    {
        $oFaker = FakerGeneratorFactory::create($sLocale);
        $mPostcode = in_array($sLocale, ['en_US', 'de_DE'], true)
            ? $oFaker->zipCode()
            : $oFaker->postcode();

        self::assertNotSame('', $oFaker->sdl());
        self::assertNotFalse(filter_var($oFaker->sdl() . '@example.com', FILTER_VALIDATE_EMAIL));
        self::assertNotSame('', $oFaker->firstName());
        self::assertNotSame('', $oFaker->lastName());
        self::assertNotSame('', $oFaker->city());
        self::assertNotSame('', $oFaker->streetAddress());
        self::assertNotSame('', (string)$mPostcode);
        self::assertNotSame('', $oFaker->paragraphs(2));
        self::assertNotFalse(filter_var($oFaker->ipv4(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
        self::assertInstanceOf(\DateTimeInterface::class, $oFaker->dateTime('-60 years', '-18 years'));
    }

    public function testUnsupportedLocaleIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Faker locale is not supported');

        new FakerFactory(1, 'female', 'unsupported_LOCALE');
    }

    public static function supportedLocaleProvider(): iterable
    {
        foreach (array_keys(FakerFactory::SUPPORTED_LOCALES) as $sLocale) {
            yield $sLocale => [$sLocale];
        }
    }

    public static function tearDownAfterClass(): void
    {
        @unlink(PH7_PATH_CACHE . 'xefi-faker-packages.php');
        @unlink(PH7_PATH_CACHE . 'xefi-faker-mixin.php');
    }
}
