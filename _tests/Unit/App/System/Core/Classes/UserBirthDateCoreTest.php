<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/UserBirthDateCore.php';

use PH7\UserBirthDateCore;
use PHPUnit\Framework\TestCase;

final class UserBirthDateCoreTest extends TestCase
{
    /**
     * @dataProvider invalidBirthDateProvider
     */
    public function testInvalidAgeFromBirthDate(string $sBirthDate): void
    {
        $iAge = UserBirthDateCore::getAgeFromBirthDate($sBirthDate);

        $this->assertSame($iAge, UserBirthDateCore::DEFAULT_AGE);
    }

    public function invalidBirthDateProvider(): array
    {
        return [
            ['01902'],
            ['01-20-2919-29'],
            ['']
        ];
    }
}
