<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/UserBirthDateCore.php';

use PH7\UserBirthDateCore;
use PHPUnit_Framework_TestCase;

class UserBirthDateCoreTest extends PHPUnit_Framework_TestCase
{
    public function testInvalidAgeFromBirthDate()
    {
        $iAge = UserBirthDateCore::getAgeFromBirthDate('01902');

        $this->assertSame($iAge, UserBirthDateCore::DEFAULT_AGE);
    }
}
