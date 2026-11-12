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
use PH7\FakerContainerFactory;
use PH7\FakerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Xefi\Faker\Container\Container;
use Xefi\Faker\Extensions\PersonExtension;

require_once PH7_PATH_SYS_MOD . 'profile-faker/inc/class/FakerFactory.php';
require_once PH7_PATH_APP . 'includes/classes/FakerContainerFactory.php';

final class FakerFactoryTest extends TestCase
{
    #[DataProvider('supportedLocaleProvider')]
    public function testXefiGeneratesProfileDataForEverySupportedLocale(string $sLocale): void
    {
        $oFactory = new FakerFactory(1, 'female', $sLocale);
        $oFaker = $this->invokePrivateMethod($oFactory, 'createFaker');
        $sEmail = $this->invokePrivateMethod($oFactory, 'getSafeEmail', $oFaker);
        $sPassword = $this->invokePrivateMethod($oFactory, 'getPassword');
        $oBirthDate = $oFaker->dateTime('-65 years', '-18 years');

        self::assertInstanceOf(Container::class, $oFaker);
        self::assertInstanceOf(\DateTimeInterface::class, $oBirthDate);
        self::assertIsString($sEmail);
        self::assertIsString($sPassword);
        self::assertNotSame('', $oFaker->firstName(PersonExtension::GENDER_FEMALE));
        self::assertNotSame('', $oFaker->lastName());
        self::assertNotSame('', $oFaker->name(PersonExtension::GENDER_MALE));
        self::assertNotSame('', $oFaker->city());
        self::assertNotSame('', $oFaker->streetAddress());
        self::assertNotSame('', $this->invokePrivateMethod($oFactory, 'getPostcode', $oFaker));
        self::assertNotSame('', $oFaker->paragraphs(2));
        self::assertSame(substr($sLocale, 3, 2), $this->invokePrivateMethod($oFactory, 'getCountryCode'));
        self::assertContains($oFaker->randomElement(['one', 'two']), ['one', 'two']);
        self::assertStringEndsWith('@example.com', $sEmail);
        self::assertNotFalse(filter_var($sEmail, FILTER_VALIDATE_EMAIL));
        self::assertSame(24, strlen($sPassword));
        self::assertNotFalse(filter_var($oFaker->ipv4(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
        $iMinimumBirthTimestamp = (new \DateTimeImmutable('-65 years'))->getTimestamp();
        $iMaximumBirthTimestamp = (new \DateTimeImmutable('-18 years'))->getTimestamp();
        self::assertGreaterThanOrEqual($iMinimumBirthTimestamp, $oBirthDate->getTimestamp());
        self::assertLessThanOrEqual($iMaximumBirthTimestamp, $oBirthDate->getTimestamp());
    }

    public function testUnsupportedLocaleIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Faker locale is not supported');

        new FakerFactory(1, 'female', 'nl_NL');
    }

    public function testInstallerSampleDataUsesAvailableXefiGenerators(): void
    {
        $oFaker = FakerContainerFactory::create('en_US');
        $sEmail = $oFaker->sdl() . '@example.com';

        self::assertNotSame('', $oFaker->sdl());
        self::assertStringEndsWith('@example.com', $sEmail);
        self::assertNotFalse(filter_var($sEmail, FILTER_VALIDATE_EMAIL));
        self::assertInstanceOf(\DateTimeInterface::class, $oFaker->dateTime('-60 years', '-18 years'));
        self::assertNotSame('', $oFaker->firstName());
        self::assertNotSame('', $oFaker->lastName());
        self::assertNotSame('', $oFaker->name());
        self::assertNotSame('', $oFaker->city());
        self::assertNotSame('', $oFaker->streetAddress());
        self::assertNotSame('', $oFaker->zipCode());
        self::assertNotSame('', $oFaker->paragraphs(2));
        self::assertNotSame('', $oFaker->phoneNumber());
        self::assertNotFalse(filter_var($oFaker->ipv4(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
    }

    public static function supportedLocaleProvider(): iterable
    {
        foreach (array_keys(FakerFactory::SUPPORTED_LOCALES) as $sLocale) {
            yield $sLocale => [$sLocale];
        }
    }

    private function invokePrivateMethod(object $oObject, string $sMethod, mixed ...$aArguments): mixed
    {
        return (new ReflectionMethod($oObject, $sMethod))->invoke($oObject, ...$aArguments);
    }
}
