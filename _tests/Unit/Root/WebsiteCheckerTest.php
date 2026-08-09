<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

require_once dirname(__DIR__, 3) . '/WebsiteChecker.php';

use PH7\WebsiteChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class WebsiteCheckerTest extends TestCase
{
    private array $aServerBackup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aServerBackup = $_SERVER;
        putenv('PH7_CANONICAL_HOST');
        putenv('PH7_TRUST_PROXY_HEADERS');
    }

    public function testCheckPhpVersionDoesNotThrowOnSupportedRuntime(): void
    {
        $oChecker = new WebsiteChecker;
        $oChecker->checkPhpVersion();

        $this->addToAssertionCount(1);
    }

    public function testInstallFolderExistsInProjectRoot(): void
    {
        $this->assertTrue((new WebsiteChecker)->doesInstallFolderExist());
    }

    public function testPinnedAuthorityMarkerIsDetectedWithoutExecutingConfig(): void
    {
        $sChecker = file_get_contents(dirname(__DIR__, 3) . '/WebsiteChecker.php');
        $sIndex = file_get_contents(dirname(__DIR__, 3) . '/index.php');

        $this->assertIsString($sChecker);
        $this->assertIsString($sIndex);
        $this->assertStringContainsString('PH7_CANONICAL_AUTHORITY_PINNED', $sChecker);
        $this->assertStringContainsString('!$oSiteChecker->doesConfigPinCanonicalAuthority()', $sIndex);
    }

    public function testNoConfigFoundMessageIsStable(): void
    {
        $this->assertSame(
            'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.',
            (new WebsiteChecker)->getNoConfigFoundMessage()
        );
    }

    public function testInstallerRedirectNeverUsesRequestControlledPhpSelf(): void
    {
        $sChecker = file_get_contents(dirname(__DIR__, 3) . '/WebsiteChecker.php');

        $this->assertIsString($sChecker);
        $this->assertStringContainsString("header('Location: ' . self::INSTALL_FOLDER_NAME);", $sChecker);
        $this->assertStringNotContainsString("\$_SERVER['PHP_SELF']", $sChecker);
    }

    public function testIncompatiblePhpVersionFlagIsFalseOnCurrentRuntime(): void
    {
        $oChecker = new WebsiteChecker;
        $oReflection = new ReflectionClass(WebsiteChecker::class);
        $oMethod = $oReflection->getMethod('isIncompatiblePhpVersion');

        $this->assertFalse($oMethod->invoke($oChecker));
    }

    public function testRequestAuthorityUsesServerNameInsteadOfRequestHost(): void
    {
        $_SERVER['SERVER_NAME'] = 'community.example.com';
        $_SERVER['HTTP_HOST'] = 'attacker.example';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_SSL'] = 'on';

        (new WebsiteChecker())->normalizeRequestAuthority();

        $this->assertSame('community.example.com', $_SERVER['HTTP_HOST']);
        $this->assertArrayNotHasKey('HTTP_X_FORWARDED_PROTO', $_SERVER);
        $this->assertArrayNotHasKey('HTTP_X_FORWARDED_SSL', $_SERVER);
    }

    public function testLegacyIpv6AuthorityKeepsItsNonStandardServerPort(): void
    {
        $_SERVER['SERVER_NAME'] = '[::1]';
        $_SERVER['SERVER_PORT'] = '8097';
        $_SERVER['HTTP_HOST'] = 'attacker.example';

        (new WebsiteChecker())->normalizeRequestAuthority();

        $this->assertSame('[::1]:8097', $_SERVER['HTTP_HOST']);
    }

    public function testExplicitCanonicalHostSupportsTrustedProxyDeployments(): void
    {
        putenv('PH7_CANONICAL_HOST=community.example.com:8443');
        putenv('PH7_TRUST_PROXY_HEADERS=1');
        $_SERVER['SERVER_NAME'] = 'php';
        $_SERVER['HTTP_HOST'] = 'attacker.example';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['SERVER_PORT'] = '8080';

        (new WebsiteChecker())->normalizeRequestAuthority();

        $this->assertSame('community.example.com:8443', $_SERVER['HTTP_HOST']);
        $this->assertSame('8443', $_SERVER['SERVER_PORT']);
        $this->assertSame('https', $_SERVER['HTTP_X_FORWARDED_PROTO']);
    }

    public function testCanonicalHttpsHostDoesNotInheritAProxyBackendPort(): void
    {
        putenv('PH7_CANONICAL_HOST=community.example.com');
        putenv('PH7_TRUST_PROXY_HEADERS=1');
        $_SERVER['SERVER_NAME'] = 'php';
        $_SERVER['SERVER_PORT'] = '8080';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

        (new WebsiteChecker())->normalizeRequestAuthority();

        $this->assertSame('community.example.com', $_SERVER['HTTP_HOST']);
        $this->assertSame('443', $_SERVER['SERVER_PORT']);
    }

    public function testInvalidExplicitCanonicalHostIsRejected(): void
    {
        putenv("PH7_CANONICAL_HOST=example.com\r\nX-Test: injected");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PH7_CANONICAL_HOST');

        (new WebsiteChecker())->normalizeRequestAuthority();
    }

    public function testInvalidServerNameWithoutCanonicalOverrideIsRejected(): void
    {
        $_SERVER['SERVER_NAME'] = '_';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('canonical ServerName');

        (new WebsiteChecker())->normalizeRequestAuthority();
    }

    #[DataProvider('invalidCanonicalPortProvider')]
    public function testInvalidExplicitCanonicalPortIsRejected(string $sHost): void
    {
        putenv('PH7_CANONICAL_HOST=' . $sHost);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PH7_CANONICAL_HOST');

        (new WebsiteChecker())->normalizeRequestAuthority();
    }

    public static function invalidCanonicalPortProvider(): array
    {
        return [
            'zero' => ['example.com:0'],
            'too high' => ['example.com:65536']
        ];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->aServerBackup;
        putenv('PH7_CANONICAL_HOST');
        putenv('PH7_TRUST_PROXY_HEADERS');

        parent::tearDown();
    }
}
