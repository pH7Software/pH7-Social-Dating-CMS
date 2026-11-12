<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Profile Faker
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\ProfileFaker;

use Faker\Factory;
use InvalidArgumentException;
use PH7\FakerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_SYS_MOD . 'profile-faker/inc/class/FakerFactory.php';

final class FakerFactoryTest extends TestCase
{
    #[DataProvider('supportedLocaleProvider')]
    public function testFakerPhpGeneratesProfileDataForEverySupportedLocale(string $sLocale): void
    {
        $oFaker = Factory::create($sLocale);

        self::assertNotSame('', $oFaker->userName);
        self::assertNotFalse(filter_var($oFaker->safeEmail, FILTER_VALIDATE_EMAIL));
        self::assertNotSame('', $oFaker->firstName);
        self::assertNotSame('', $oFaker->lastName);
        self::assertNotSame('', $oFaker->city);
        self::assertNotSame('', $oFaker->streetAddress);
        self::assertNotSame('', (string)$oFaker->postcode);
        self::assertNotSame('', $oFaker->paragraph(2));
        self::assertNotFalse(filter_var($oFaker->ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4));
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
}
