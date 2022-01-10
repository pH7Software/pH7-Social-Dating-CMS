<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

use PH7\GenderTypeUserCore;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_SYS . 'core/classes/GenderTypeUserCore.php';

final class GenderTypeUserCoreTest extends TestCase
{
    /**
     * @dataProvider validGenderTypesProvider
     */
    public function testValidGenders(string $sGender, string $bIncludeCoupleGender)
    {
        $bResult = GenderTypeUserCore::isGenderValid($sGender, $bIncludeCoupleGender);

        $this->assertTrue($bResult);
    }

    /**
     * @dataProvider invalidGenderTypesProvider
     */
    public function testInvalidGenders(string $sGender, string $bIncludeCoupleGender): void
    {
        $bResult = GenderTypeUserCore::isGenderValid($sGender, $bIncludeCoupleGender);

        $this->assertFalse($bResult);
    }

    public function validGenderTypesProvider(): array
    {
        return [
            ['male', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['female', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['male', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['female', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['couple', GenderTypeUserCore::CONSIDER_COUPLE_GENDER]
        ];
    }

    public function invalidGenderTypesProvider(): array
    {
        return [
            ['couple', GenderTypeUserCore::IGNORE_COUPLE_GENDER],
            ['visitor', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['woman', GenderTypeUserCore::CONSIDER_COUPLE_GENDER],
            ['man', GenderTypeUserCore::IGNORE_COUPLE_GENDER]
        ];
    }
}
