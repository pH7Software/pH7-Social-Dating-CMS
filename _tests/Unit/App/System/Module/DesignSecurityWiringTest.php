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

final class DesignSecurityWiringTest extends TestCase
{
    /**
     * Templates render CSRF tokens through $designSecurity. If the controller owning the
     * template never assigns it, the page dies with "Call to a member function on null",
     * as the Module Manager did after its install/uninstall forms gained a token.
     */
    public function testControllersProvideDesignSecurityToTheirTemplates(): void
    {
        $aMissing = [];

        foreach (glob(PH7_PATH_SYS_MOD . '*/views/*/tpl/*/*.tpl') as $sTemplate) {
            if (strpos(file_get_contents($sTemplate), '$designSecurity') === false) {
                continue;
            }

            $sModuleDir = dirname($sTemplate, 5);
            $sController = ucfirst(basename(dirname($sTemplate))) . 'Controller.php';
            $sControllerFile = $sModuleDir . '/controllers/' . $sController;

            if (is_file($sControllerFile)
                && !preg_match('/\$this->view->designSecurity\s*=/', file_get_contents($sControllerFile))
            ) {
                $aMissing[] = basename($sModuleDir) . '/' . $sController . ' (used by ' . basename($sTemplate) . ')';
            }
        }

        $this->assertSame([], $aMissing);
    }
}
