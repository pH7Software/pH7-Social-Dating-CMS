<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Controller;

use MaxMind\Db\Reader\InvalidDatabaseException;
use PH7\Framework\Mvc\Controller\Controller;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;
use PH7\TwoFactorAuthCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CountryRestrictionTest extends TestCase
{
    #[DataProvider('provideTwoFactorCases')]
    public function testOnlyCurrentAdminVerificationCanRecoverWithoutGeoIp(
        string $sChallengeModule,
        string $sRequestedModule,
        string $sAction,
        bool $bExpired,
        bool $bCorrupt,
        bool $bAllowed
    ): void {
        class_alias(CountryRestrictionAdminStub::class, 'PH7\\AdminCore');
        class_alias(CountryRestrictionModelStub::class, 'PH7\\Framework\\Mvc\\Model\\BlockCountry');
        CountryRestrictionModelStub::$aCountries = ['UK'];
        $sPath = sys_get_temp_dir() . '/ph7-admin-recovery-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($sPath, 0700));
        copy(PH7_PATH_FRAMEWORK . 'Geo/Ip/Geo.class.php', $sPath . '/Geo.class.php');
        require $sPath . '/Geo.class.php';
        if ($bCorrupt) {
            file_put_contents($sPath . '/GeoLite2-City.mmdb', 'invalid database');
        }
        $sErrorLog = ini_set('error_log', $sPath . '/error.log');
        $oSession = (new ReflectionClass(Session::class))->newInstanceWithoutConstructor();
        if ($sChallengeModule !== '') {
            TwoFactorAuthCore::beginChallenge($oSession, $sChallengeModule, 42);
        }
        if ($bExpired) {
            $oSession->set('2fa_expires', time() - 1);
        }
        $_GET['mod'] = $sRequestedModule;
        $oController = new class extends Controller {
            public function __construct()
            {
            }
        };
        $oRegistry = Registry::getInstance();
        $oRegistry->module = 'two-factor-auth';
        $oRegistry->controller = 'MainController';
        $oRegistry->action = $sAction;
        foreach (['registry' => $oRegistry, 'session' => $oSession, 'httpRequest' => new Http()] as $sName => $oValue) {
            (new ReflectionProperty($oController, $sName))->setValue($oController, $oValue);
        }
        try {
            if (!$bAllowed) {
                $this->expectException(InvalidDatabaseException::class);
            }
            self::assertFalse((new ReflectionMethod(Controller::class, 'isBlockedCountryPageEligible'))->invoke($oController));
        } finally {
            TwoFactorAuthCore::clearChallenge($oSession);
            ini_set('error_log', $sErrorLog);
            foreach (glob($sPath . '/*') as $sFile) {
                unlink($sFile);
            }
            rmdir($sPath);
        }
    }

    public static function provideTwoFactorCases(): array
    {
        return [
            'admin, missing database' => [PH7_ADMIN_MOD, PH7_ADMIN_MOD, 'verificationcode', false, false, true],
            'admin, corrupt database' => [PH7_ADMIN_MOD, PH7_ADMIN_MOD, 'verificationcode', false, true, true],
            'member' => ['user', 'user', 'verificationcode', false, false, false],
            'affiliate' => ['affiliate', 'affiliate', 'verificationcode', false, true, false],
            'member requesting admin route' => ['user', PH7_ADMIN_MOD, 'verificationcode', false, false, false],
            'admin requesting member route' => [PH7_ADMIN_MOD, 'user', 'verificationcode', false, false, false],
            'expired admin' => [PH7_ADMIN_MOD, PH7_ADMIN_MOD, 'verificationcode', true, false, false],
            'missing challenge' => ['', PH7_ADMIN_MOD, 'verificationcode', false, false, false],
            'setup is not recovery' => [PH7_ADMIN_MOD, PH7_ADMIN_MOD, 'setup', false, false, false]
        ];
    }

    #[DataProvider('provideAccessCases')]
    public function testCountryRestrictionsKeepTheirRecoveryBoundary(
        array $aBlockedCountries,
        bool $bUnavailable,
        bool $bAdmin,
        string $sModule,
        bool $bBlocked,
        bool $bExpectFailure,
        int $iExpectedLookups
    ): void {
        class_alias(CountryRestrictionAdminStub::class, 'PH7\\AdminCore');
        class_alias(CountryRestrictionModelStub::class, 'PH7\\Framework\\Mvc\\Model\\BlockCountry');
        class_alias(CountryRestrictionGeoStub::class, 'PH7\\Framework\\Geo\\Ip\\Geo');
        CountryRestrictionAdminStub::$bAuthenticated = $bAdmin;
        CountryRestrictionModelStub::$aCountries = $aBlockedCountries;
        CountryRestrictionGeoStub::$bUnavailable = $bUnavailable;

        $oController = new class extends Controller {
            public function __construct()
            {
            }
        };
        $oRegistry = Registry::getInstance();
        $oRegistry->module = $sModule;
        (new ReflectionProperty($oController, 'registry'))->setValue($oController, $oRegistry);
        $oMethod = new ReflectionMethod(Controller::class, 'isBlockedCountryPageEligible');
        if ($bExpectFailure) {
            $this->expectException(InvalidDatabaseException::class);
        }
        try {
            self::assertSame($bBlocked, $oMethod->invoke($oController));
        } finally {
            self::assertSame($iExpectedLookups, CountryRestrictionGeoStub::$iLookups);
        }
    }

    public static function provideAccessCases(): array
    {
        return [
            'optional location' => [[], true, false, 'user', false, false, 0],
            'admin recovery module' => [['UK'], true, false, PH7_ADMIN_MOD, false, false, 0],
            'authenticated admin' => [['UK'], true, true, 'user', false, false, 0],
            'blocked country' => [['UK'], false, false, 'user', true, false, 1],
            'allowed country' => [['FR'], false, false, 'user', false, false, 1],
            'no bypass on database failure' => [['UK'], true, false, 'user', false, true, 1]
        ];
    }
}

final class CountryRestrictionAdminStub
{
    public static bool $bAuthenticated = false;

    public static function auth(): bool
    {
        return self::$bAuthenticated;
    }
}

final class CountryRestrictionModelStub
{
    public static array $aCountries = [];

    public function getBlockedCountries(): array
    {
        return self::$aCountries;
    }

    public function isBlocked(string $sCountryCode): bool
    {
        return in_array($sCountryCode, self::$aCountries, true);
    }
}

final class CountryRestrictionGeoStub
{
    public static bool $bUnavailable = false;
    public static int $iLookups = 0;

    public static function getCountryCode($sIpAddress = null, bool $bRequireDatabase = false): ?string
    {
        ++self::$iLookups;
        if (self::$bUnavailable) {
            if ($bRequireDatabase) {
                throw new InvalidDatabaseException('Database unavailable');
            }

            return null;
        }

        return 'GB';
    }
}
