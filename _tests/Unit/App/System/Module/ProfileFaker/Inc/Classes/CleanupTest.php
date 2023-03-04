<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / ProfileFaker / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\ProfileFaker\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'profile-faker/inc/class/Cleanup.php';

use PH7\Cleanup;
use PHPUnit\Framework\TestCase;

final class CleanupTest extends TestCase
{
    /**
     * @dataProvider usernamesProvider
     */
    public function testUsername(string $sActualUsername, string $sExpectedUsername, int $iMaxLength)
    {
        $sCleanedUsername = Cleanup::username($sActualUsername, $iMaxLength);
        $this->assertSame($sCleanedUsername, $sExpectedUsername);
    }

    public function usernamesProvider(): array
    {
        return [
            ['pierre.soria', 'pierre-soria', 40],
            ['pierre-henry soria', 'pierre-henry-soria', 40],
            ['Marie-Amelie.Rollier', 'Marie-Amelie-R', 14],
            ['marie..heloise Beghin', 'marie--helois', 13],
            ['.', '', 0]
        ];
    }
}
