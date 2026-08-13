<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PH7\Database;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class InstallerHardeningTest extends TestCase
{
    public function testInstallerDatabaseDsnIncludesConfiguredPortAndCharset(): void
    {
        require_once dirname(__DIR__, 3) . '/_install/library/Database.class.php';

        $oReflection = new ReflectionClass(Database::class);
        $oDatabase = $oReflection->newInstanceWithoutConstructor();
        $oBuildDsn = $oReflection->getMethod('buildDsn');

        $this->assertSame(
            'mysql:host=db.internal;port=3307;dbname=ph7;charset=utf8mb4',
            $oBuildDsn->invoke(
                $oDatabase,
                [
                    'db_type' => 'mysql',
                    'db_hostname' => 'db.internal',
                    'db_port' => 3307,
                    'db_name' => 'ph7',
                    'db_charset' => 'utf8mb4'
                ]
            )
        );
    }

    public function testEveryInstallerPostFormCarriesAnActionToken(): void
    {
        $sViewDirectory = dirname(__DIR__, 3) . '/_install/views/base';
        $aPostTemplates = [
            'index.tpl',
            'license.tpl',
            'config_path.tpl',
            'config_system.tpl',
            'config_site.tpl',
            'niche.tpl',
            'finish.tpl'
        ];

        foreach ($aPostTemplates as $sTemplate) {
            $sContents = file_get_contents($sViewDirectory . '/' . $sTemplate);

            $this->assertIsString($sContents);
            $this->assertStringContainsString('method="post"', strtolower($sContents));
            $this->assertStringContainsString('name="action_token"', $sContents);
        }
    }

    public function testInstallerAccessUsesAnExplicitIndexRouteWithoutUrlRewriting(): void
    {
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');
        $sRouter = $this->readProjectFile('_install/inc/init.inc.php');
        $sIndexView = $this->readProjectFile('_install/views/base/index.tpl');

        $this->assertStringContainsString("redirect(PH7_URL_SLUG_INSTALL . 'index');", $sController);
        $this->assertStringContainsString("redirect(PH7_URL_SLUG_INSTALL . 'index');", $sRouter);
        $this->assertStringContainsString('action="{$smarty.const.PH7_URL_SLUG_INSTALL}index"', $sIndexView);
        $this->assertStringNotContainsString('action="{$smarty.const.PH7_URL_SLUG_INSTALL}"', $sIndexView);
    }

    public function testRotatingTheInstallerTokenRevokesAuthenticatedSessions(): void
    {
        $sController = $this->readProjectFile('_install/library/Controller.class.php');

        $this->assertStringContainsString('hash_equals($sExpectedHash, $sAuthenticatedHash)', $sController);
        $this->assertStringContainsString('$_SESSION[self::ACCESS_SESSION_KEY] = $sExpectedHash;', $sController);
        $this->assertStringNotContainsString('$_SESSION[self::ACCESS_SESSION_KEY] = true;', $sController);
    }

    public function testInstallerUsesTransactionalMandatoryDatabaseSteps(): void
    {
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');

        $this->assertGreaterThanOrEqual(2, substr_count($sController, '->beginTransaction()'));
        $this->assertGreaterThanOrEqual(2, substr_count($sController, '->rollBack()'));
        $this->assertStringContainsString('if (remove_install_dir())', $sController);
        $this->assertStringContainsString(
            'if (!$this->doesInitialAdminMatch($DB, $sAdminPassword))',
            $sController
        );
        $this->assertStringContainsString('SELECT username, password, email FROM ', $sController);
        $this->assertStringContainsString(
            'Security::checkPwd($sAdminPassword, (string)$aAdmin[\'password\'])',
            $sController
        );
        $this->assertStringContainsString("? \$LANG['initial_admin_mismatch']", $sController);
        $this->assertStringContainsString('if ($this->canResumeCompletedDatabaseStep($DB))', $sController);
        $this->assertStringNotContainsString("escape(\$oE->getMessage())", $sController);
    }

    public function testDurableProgressRewindsWhenRequiredArtifactsAreLost(): void
    {
        $sFunctions = $this->readProjectFile('_install/inc/fns/misc.php');
        $sController = $this->readProjectFile('_install/library/Controller.class.php');

        $this->assertStringContainsString("!is_file(PH7_ROOT_PUBLIC . '_constants.php')", $sFunctions);
        $this->assertStringContainsString('!has_valid_protected_install_path($aContext)', $sFunctions);
        $this->assertStringContainsString('!has_installed_database_config($aContext)', $sFunctions);
        $this->assertStringContainsString("['protected_path' => check_ext_end(\$sRealProtectedPath)]", $this->readProjectFile('_install/controllers/InstallController.php'));
        $this->assertStringContainsString("unset(\$_SESSION['step' . \$iStep]);", $sController);
        $this->assertStringContainsString("unset(\$_SESSION['db'], \$_SESSION['val']", $sController);
        $this->assertStringContainsString("\$iCompletedStep >= 4 && isset(\$aContext['database_prefix'])", $sController);
        $this->assertStringContainsString("\$iCompletedStep >= 5 && isset(\$aContext['admin_login_email'])", $sController);
        $this->assertStringContainsString('sanitize_install_state_context($aContext, $iCompletedStep)', $sFunctions);
        $this->assertStringContainsString('replaceConstantsFile(', $this->readProjectFile('_install/controllers/InstallController.php'));
        $this->assertStringContainsString('getUnwritableApplicationPaths(', $this->readProjectFile('_install/controllers/InstallController.php'));
        $this->assertStringContainsString('runtime_path_not_writable', $this->readProjectFile('_install/langs/en/install.lang.php'));
    }

    public function testInstallerRequiresAuditedMySqlVersion(): void
    {
        $sConstants = $this->readProjectFile('_install/constants.php');
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');

        $this->assertStringContainsString("define('PH7_REQUIRED_SQL_VERSION', '8.0.0');", $sConstants);
        $this->assertStringContainsString("stripos(\$sVersion, 'mariadb') === false", $sController);
        $this->assertStringContainsString(
            "PH7_ROOT_INSTALL . 'data/caches' => 'the installer state and access-token directory'",
            $this->readProjectFile('_install/requirements.php')
        );
    }

    public function testInstallerDefaultsUseCurrentProductNameForDatabase(): void
    {
        require_once dirname(__DIR__, 3) . '/_install/library/DbDefaultConfig.class.php';
        require_once dirname(__DIR__, 3) . '/_tools/cli/Misc/Database/DbDefaultConfig.php';

        $this->assertSame('ph7builder', \PH7\DbDefaultConfig::NAME);
        $this->assertSame('ph7builder', \PH7\Cli\Misc\Database\DbDefaultConfig::NAME);
    }

    public function testInstallerProgressEndsAtExactlyOneHundredPercent(): void
    {
        $sHeader = $this->readProjectFile('_install/views/base/inc/header.tpl');

        $this->assertStringContainsString(
            '($sept_number/$total_install_steps*100)|round',
            $sHeader
        );
        $this->assertStringNotContainsString('$sept_number*14.3', $sHeader);
    }

    public function testInstallerRequiresServerSideFileTypeDetection(): void
    {
        $sRequirements = $this->readProjectFile('_install/requirements.php');
        $aComposer = json_decode($this->readProjectFile('composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString("'fileinfo' => 'Fileinfo'", $sRequirements);
        $this->assertSame('*', $aComposer['require']['ext-fileinfo'] ?? null);
    }

    public function testRewriteDetectionNeverRequestsAHostDerivedUrl(): void
    {
        $sFunctions = $this->readProjectFile('_install/inc/fns/misc.php');

        $this->assertStringContainsString("getenv('HTTP_MOD_REWRITE')", $sFunctions);
        $this->assertStringContainsString('bin2hex(random_bytes(', $sFunctions);
        $this->assertStringNotContainsString('mt_rand()', $sFunctions);
        $this->assertStringNotContainsString('uniqid(', $sFunctions);
        $this->assertStringNotContainsString('CURLOPT_FOLLOWLOCATION', $sFunctions);
        $this->assertStringNotContainsString("PH7_URL_INSTALL . 'test_mod_rewrite'", $sFunctions);
    }

    public function testInstallerPinsCanonicalAuthorityWithoutTrustingRequestHeaders(): void
    {
        $sInstallerConstants = $this->readProjectFile('_install/constants.php');
        $sGeneratedConstants = $this->readProjectFile('_install/data/configs/constants.php');
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');

        $this->assertStringNotContainsString("\$_SERVER['HTTP_HOST']", $sInstallerConstants);
        $this->assertStringContainsString("getenv('PH7_CANONICAL_HOST')", $sInstallerConstants);
        $this->assertStringContainsString("getenv('PH7_TRUST_PROXY_HEADERS') === '1'", $sInstallerConstants);
        $this->assertStringContainsString('(int)$aServerNameMatches[1] < 1', $sInstallerConstants);
        $this->assertStringContainsString("\$sUrlProtocol = '%url_protocol%';", $sGeneratedConstants);
        $this->assertStringContainsString("\$sDomain = '%domain%';", $sGeneratedConstants);
        $this->assertStringContainsString("define('PH7_CANONICAL_AUTHORITY_PINNED', true);", $sGeneratedConstants);
        $this->assertStringNotContainsString("\$_SERVER['HTTP_HOST']", $sGeneratedConstants);
        $this->assertStringContainsString("'%url_protocol%'", $sController);
        $this->assertStringContainsString("'%domain%'", $sController);
        $this->assertStringContainsString("!str_starts_with(\$sCanonicalHost, '[')", $sController);
    }

    public function testInstallerDoesNotTrustClientSuppliedProxyHeadersForAdminIp(): void
    {
        $sFunctions = $this->readProjectFile('_install/inc/fns/misc.php');
        $iMethodStart = strpos($sFunctions, 'function client_ip()');
        $iMethodEnd = strpos($sFunctions, '/**', $iMethodStart);
        $sMethod = substr($sFunctions, $iMethodStart, $iMethodEnd - $iMethodStart);

        $this->assertIsString($sMethod);
        $this->assertStringContainsString("\$_SERVER['REMOTE_ADDR']", $sMethod);
        $this->assertStringContainsString('FILTER_VALIDATE_IP', $sMethod);
        $this->assertStringNotContainsString('HTTP_CLIENT_IP', $sMethod);
        $this->assertStringNotContainsString('HTTP_X_FORWARDED_FOR', $sMethod);
    }

    public function testOptionalSampleInitializationIsInsideItsFailureBoundary(): void
    {
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');
        $iMethodStart = strpos($sController, 'private function tryToPopulateSampleData(): void');
        $iMethodEnd = strpos($sController, 'private static function isSupportedMySqlServer', $iMethodStart);
        $sMethod = substr($sController, $iMethodStart, $iMethodEnd - $iMethodStart);

        $this->assertIsString($sMethod);
        $this->assertLessThan(
            strpos($sMethod, '->_initializeDatabase()'),
            strpos($sMethod, 'try {')
        );
    }

    public function testConfigPlaceholdersAreReplacedWithoutRescanningCredentials(): void
    {
        $sController = $this->readProjectFile('_install/controllers/InstallController.php');
        $sTemplate = 'password = "%db_password%"' . "\n" . 'name = "%db_name%"';

        $this->assertStringContainsString('return strtr(', $sController);
        $this->assertSame(
            'password = "%db_name%"' . "\n" . 'name = "launch_database"',
            strtr(
                $sTemplate,
                [
                    '%db_password%' => '%db_name%',
                    '%db_name%' => 'launch_database'
                ]
            )
        );
    }

    public function testInstallerResumeDecodesEscapedDatabaseCredentialsLikeRuntimeConfig(): void
    {
        $sDatabaseConnect = $this->readProjectFile('_install/inc/_db_connect.inc.php');
        putenv('PH7_INSTALLER_INI_EXPANSION=wrong-value');
        $sPassword = 'a\\b"c${PH7_INSTALLER_INI_EXPANSION}';
        $sEscapedPassword = strtr(
            $sPassword,
            ["\\" => "\\\\", '$' => '\\$', '"' => '\\"']
        );
        $sTemporaryIni = tempnam(sys_get_temp_dir(), 'ph7-installer-ini-');

        $this->assertIsString($sTemporaryIni);
        file_put_contents($sTemporaryIni, "[database]\npassword = \"{$sEscapedPassword}\"\n");

        try {
            $aConfig = parse_ini_file($sTemporaryIni, true, INI_SCANNER_TYPED);

            $this->assertSame($sPassword, $aConfig['database']['password']);
            $this->assertStringContainsString('INI_SCANNER_TYPED', $sDatabaseConnect);
            $this->assertStringNotContainsString('INI_SCANNER_RAW', $sDatabaseConnect);
        } finally {
            unlink($sTemporaryIni);
            putenv('PH7_INSTALLER_INI_EXPANSION');
        }
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(dirname(__DIR__, 3) . '/' . $sRelativePath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
