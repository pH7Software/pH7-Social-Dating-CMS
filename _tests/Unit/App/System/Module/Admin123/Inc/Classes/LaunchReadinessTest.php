<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/LaunchReadiness.php';

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Server\Environment;
use PH7\Framework\Str\Str;
use PH7\LaunchReadiness;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LaunchReadinessTest extends TestCase
{
    public function testCompleteProductionSiteReportsEveryCheckAsReady(): void
    {
        $oReadiness = new LaunchReadiness(
            'https://community.example/',
            false,
            Environment::PRODUCTION_MODE,
            DbConfig::ENABLED_SITE,
            'a-long-random-cron-secret'
        );

        $this->assertSame(
            [
                LaunchReadiness::HTTPS => true,
                LaunchReadiness::INSTALLER_REMOVED => true,
                LaunchReadiness::PRODUCTION_MODE => true,
                LaunchReadiness::SITE_ONLINE => true,
                LaunchReadiness::CRON_SECRET => true
            ],
            $oReadiness->checks()
        );
        $this->assertSame(5, $oReadiness->completedCount());
        $this->assertSame(5, $oReadiness->totalCount());
        $this->assertTrue($oReadiness->isComplete());
    }

    public function testDashboardTemplateRendersEscapedReadinessActions(): void
    {
        $oSyntax = new Curly();
        $oSyntax->setCode(file_get_contents(
            PH7_PATH_SYS_MOD . 'admin123/views/base/tpl/main/get_started_intro.inc.tpl'
        ));
        $oSyntax->parse();

        $launch_readiness_completed = 0;
        $launch_readiness_total = 1;
        $launch_readiness_checks = [[
            'is_complete' => false,
            'label' => 'Review <script>alert(1)</script>',
            'action_label' => 'Fix configuration',
            'action_url' => 'https://example.test/" onmouseover="alert(1)'
        ]];
        $launch_checklist_url = 'https://example.test/checklist';
        $str = new Str();

        ob_start();
        try {
            eval('?>' . $oSyntax->getParsedCode());
            $sHtml = (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $oDocument = new \DOMDocument();
        $oDocument->loadHTML($sHtml, LIBXML_NOERROR | LIBXML_NOWARNING);

        $this->assertSame(0, $oDocument->getElementsByTagName('script')->length);
        $this->assertSame(2, $oDocument->getElementsByTagName('a')->length);
        $this->assertFalse($oDocument->getElementsByTagName('a')->item(0)->hasAttribute('onmouseover'));
        $this->assertSame('launch-readiness-progress', $oDocument->getElementsByTagName('progress')->item(0)->getAttribute('aria-labelledby'));
    }

    #[DataProvider('incompleteConfigurationProvider')]
    public function testOneIncompleteRequirementKeepsTheSiteFromReady(
        string $sSiteUrl,
        bool $bInstallerPresent,
        string $sEnvironment,
        string $sSiteStatus,
        string $sCronSecurityHash,
        string $sIncompleteCheck
    ): void {
        $oReadiness = new LaunchReadiness(
            $sSiteUrl,
            $bInstallerPresent,
            $sEnvironment,
            $sSiteStatus,
            $sCronSecurityHash
        );

        $aChecks = $oReadiness->checks();

        $this->assertFalse($aChecks[$sIncompleteCheck]);
        $this->assertSame(4, $oReadiness->completedCount());
        $this->assertFalse($oReadiness->isComplete());
    }

    public static function incompleteConfigurationProvider(): array
    {
        return [
            'HTTP site URL' => [
                'http://community.example/', false, Environment::PRODUCTION_MODE, DbConfig::ENABLED_SITE,
                'cron-secret', LaunchReadiness::HTTPS
            ],
            'invalid site URL' => [
                'not-a-url', false, Environment::PRODUCTION_MODE, DbConfig::ENABLED_SITE,
                'cron-secret', LaunchReadiness::HTTPS
            ],
            'installer still present' => [
                'https://community.example/', true, Environment::PRODUCTION_MODE, DbConfig::ENABLED_SITE, 'cron-secret',
                LaunchReadiness::INSTALLER_REMOVED
            ],
            'development environment' => [
                'https://community.example/', false, 'development', DbConfig::ENABLED_SITE, 'cron-secret',
                LaunchReadiness::PRODUCTION_MODE
            ],
            'maintenance mode' => [
                'https://community.example/', false, Environment::PRODUCTION_MODE, DbConfig::MAINTENANCE_SITE, 'cron-secret',
                LaunchReadiness::SITE_ONLINE
            ],
            'blank cron secret' => [
                'https://community.example/', false, Environment::PRODUCTION_MODE, DbConfig::ENABLED_SITE,
                '   ', LaunchReadiness::CRON_SECRET
            ]
        ];
    }
}
