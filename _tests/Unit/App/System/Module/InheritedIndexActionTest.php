<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module;

use PHPUnit\Framework\TestCase;

final class InheritedIndexActionTest extends TestCase
{
    /**
     * A controller extending its module's MainController inherits index(), which renders
     * "<controller>/index.tpl". Without its own index() or that template, the controller's
     * bare URL (e.g. /video/admin) failed with a server error instead of loading a page.
     */
    public function testControllersExtendingMainControllerCanServeTheirIndex(): void
    {
        $aBroken = [];

        foreach (glob(PH7_PATH_SYS_MOD . '*/controllers/*Controller.php') as $sController) {
            $sSource = file_get_contents($sController);
            if (!preg_match('/class (\w+)Controller extends MainController\b/', $sSource, $aMatch)) {
                continue;
            }

            $sModuleDir = dirname($sController, 2);
            $sMainSource = file_get_contents($sModuleDir . '/controllers/MainController.php');
            $bInheritsIndex = preg_match('/public function index\s*\(/', $sMainSource)
                && !preg_match('/public function index\s*\(/', $sSource);
            $sTemplate = $sModuleDir . '/views/base/tpl/' . strtolower($aMatch[1]) . '/index.tpl';

            if ($bInheritsIndex && !is_file($sTemplate)) {
                $aBroken[] = basename($sModuleDir) . '/' . basename($sController);
            }
        }

        $this->assertSame([], $aBroken);
    }
}
