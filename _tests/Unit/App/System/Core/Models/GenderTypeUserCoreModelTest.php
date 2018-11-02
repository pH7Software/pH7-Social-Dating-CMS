<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Models
 */

namespace PH7\Test\Unit\App\System\Core\Models;

use PH7\GenderTypeUserCoreModel;
use PHPUnit_Framework_TestCase;

require_once PH7_PATH_SYS . 'core/models/GenderTypeUserCoreModel.php';

class GenderTypeUserCoreModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sGender
     * @param bool $bIncludeCoupleGender
     *
     * @dataProvider validGenderTypesProvider
     */
    public function testValidGenders($sGender, $bIncludeCoupleGender)
    {
        $bResult = GenderTypeUserCoreModel::isGenderValid($sGender, $bIncludeCoupleGender);

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
        $bResult = GenderTypeUserCoreModel::isGenderValid($sGender, $bIncludeCoupleGender);

        $this->assertFalse($bResult);
    }

    /**
     * @return array
     */
    public function validGenderTypesProvider()
    {
        return [
            ['male', GenderTypeUserCoreModel::CONSIDER_COUPLE_GENDER],
            ['female', GenderTypeUserCoreModel::CONSIDER_COUPLE_GENDER],
            ['male', GenderTypeUserCoreModel::IGNORE_COUPLE_GENDER],
            ['female', GenderTypeUserCoreModel::IGNORE_COUPLE_GENDER],
            ['couple', GenderTypeUserCoreModel::CONSIDER_COUPLE_GENDER]
        ];
    }

    /**
     * @return array
     */
    public function invalidGenderTypesProvider()
    {
        return [
            ['couple', GenderTypeUserCoreModel::IGNORE_COUPLE_GENDER],
            ['visitor', GenderTypeUserCoreModel::CONSIDER_COUPLE_GENDER],
            ['woman', GenderTypeUserCoreModel::CONSIDER_COUPLE_GENDER],
            ['man', GenderTypeUserCoreModel::IGNORE_COUPLE_GENDER]
        ];
    }
}