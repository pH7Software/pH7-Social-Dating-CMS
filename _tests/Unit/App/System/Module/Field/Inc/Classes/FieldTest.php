<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Field / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Field\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'field/inc/class/Field.php';

use PH7\Field;
use PHPUnit_Framework_TestCase;

class FieldTest extends PHPUnit_Framework_TestCase
{
    const PHONE_FIELD_NAME = 'phone';
    const CUSTOM_FIELD_NAME = 'myownfield';

    public function testAffTable()
    {
        $sResult = Field::getTable('aff');

        $this->assertSame('affiliates_info', $sResult);
    }

    public function testUserTable()
    {
        $sResult = Field::getTable('user');

        $this->assertSame('members_info', $sResult);
    }

    public function testModifiableField()
    {
        $bResult = Field::unmodifiable(self::CUSTOM_FIELD_NAME);

        $this->assertFalse($bResult);
    }

    public function testUnmodifiableField()
    {
        $bResult = Field::unmodifiable(self::PHONE_FIELD_NAME);

        $this->assertTrue($bResult);
    }
}
