<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

use PH7\GenderTypeUserCore;
use PHPUnit_Framework_TestCase;

require_once PH7_PATH_SYS . 'core/classes/GenderTypeUserCore.php';

class GenderTypeUserCoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sGender
     * @param bool $bIncludeCoupleGender
     *
     * @dataProvider validGenderTypesProvider
     */
    public function testValidGenders($sGender, $bIncludeCoupleGender)
    {
        $bResult = GenderTypeUserCore::isGenderValid($sGender, $bIncludeCoupleGender);

        $this->assertTrue($bResult);
    }

    /**
     * @param string $sGender
     * @param bool $bIncludeCoupleGender
     *
     * @dataProvider invalidGenderTypesProvider
     */
    public function testInvalidGenders($sGender, $bIncludeCoupleGender)
    {
        $bResult = GenderTypeUserCore::isGenderValid($sGender, $bIncludeCoupleGender);

        $this->assertFalse($bResult);
    }

    /**
     * @return array
     */
    public function validGenderTypesProvider()
    {
        return [
            ['male', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['female', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['male', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['female', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['couple', GenderTypeUserCore::CONSIDER_COUPLE_GENDER]
        ];
    }

    /**
     * @return array
     */
    public function invalidGenderTypesProvider()
    {
        return [
            ['couple', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['visitor', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['woman', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['man', GenderTypeUserCore::IGNORE_COUPLE_GENDER]
        ];
    }
}
