<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / ProfileFaker / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\ProfileFaker\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'profile-faker/inc/class/Cleanup.php';

use PH7\Cleanup;
use PHPUnit_Framework_TestCase;

class CleanupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sActualUsername
     * @param string $sExpectedUsername
     * @param int $iMaxLength
     *
     * @dataProvider usernamesProvider
     */
    public function testUsername($sActualUsername, $sExpectedUsername, $iMaxLength)
    {
        $sCleanedUsername = Cleanup::username($sActualUsername, $iMaxLength);
        $this->assertSame($sCleanedUsername, $sExpectedUsername);
    }

    /**
     * @return array
     */
    public function usernamesProvider()
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
