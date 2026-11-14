<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FrontEndPerformanceBudgetTest extends TestCase
{
    private const MAX_DEFAULT_CSS_BYTES = 320 * 1024;
    private const MAX_DEFAULT_JS_BYTES = 460 * 1024;

    private const DEFAULT_CSS_ASSETS = [
        'static/css/js/jquery/smoothness/jquery-ui.css',
        'static/css/font-awesome.css',
        'static/css/js/jquery/box/box.css',
        'static/css/bootstrap.css',
        'static/css/bootstrap_customize.css',
        'static/css/animate.css',
        'templates/themes/base/css/common.css',
        'templates/themes/base/css/style.css',
        'templates/themes/base/css/layout.css',
        'templates/themes/base/css/like.css',
        'templates/themes/base/css/color.css',
        'templates/themes/base/css/form.css',
        'templates/themes/base/css/js/jquery/rating.css',
        'templates/themes/base/css/js/jquery/apprise.css',
        'templates/themes/base/css/js/jquery/tipTip.css',
        'templates/themes/base/css/design_system.css',
        'templates/themes/base/css/menu.css'
    ];

    private const DEFAULT_JS_ASSETS = [
        'static/js/jquery/jquery.js',
        'static/js/jquery/box.js',
        'static/js/jquery/tipTip.js',
        'static/js/bootstrap.js',
        'static/js/common.js',
        'static/js/str.js',
        'static/js/holder.js',
        'static/js/password_toggle.js',
        'templates/themes/base/js/global.js',
        'static/js/jquery/jquery-ui.js'
    ];

    #[DataProvider('defaultAssetBudgetProvider')]
    public function testDefaultLayoutAssetsStayWithinTheirUncompressedBudget(array $aAssets, int $iBudget): void
    {
        $iTotalBytes = 0;

        foreach ($aAssets as $sAsset) {
            $sAssetPath = self::projectRoot() . '/' . $sAsset;
            $this->assertFileExists($sAssetPath);
            $iAssetBytes = filesize($sAssetPath);
            $this->assertIsInt($iAssetBytes);
            $iTotalBytes += $iAssetBytes;
        }

        $this->assertLessThanOrEqual(
            $iBudget,
            $iTotalBytes,
            sprintf('Default assets use %d bytes, above the %d-byte performance budget.', $iTotalBytes, $iBudget)
        );
    }

    public static function defaultAssetBudgetProvider(): array
    {
        return [
            'CSS' => [self::DEFAULT_CSS_ASSETS, self::MAX_DEFAULT_CSS_BYTES],
            'JavaScript' => [self::DEFAULT_JS_ASSETS, self::MAX_DEFAULT_JS_BYTES]
        ];
    }

    private static function projectRoot(): string
    {
        return dirname(PH7_PATH_PROTECTED);
    }
}
