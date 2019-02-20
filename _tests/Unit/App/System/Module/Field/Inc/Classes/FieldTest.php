<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
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
    const PUNCHLINE_FIELD_NAME = 'punchline';
    const CUSTOM_FIELD_NAME = 'myownfield';

    public function testUserTable()
    {
        $sResult = Field::getTable('user');

        $this->assertSame('members_info', $sResult);
    }

    public function testAffiliateTable()
    {
        $sResult = Field::getTable('aff');

        $this->assertSame('affiliates_info', $sResult);
    }

    public function testUserModifiableField()
    {
        $bResult = Field::unmodifiable('user', self::CUSTOM_FIELD_NAME);

        $this->assertFalse($bResult);
    }

    public function testAffiliateModifiableField()
    {
        $bResult = Field::unmodifiable('aff', self::PUNCHLINE_FIELD_NAME);

        $this->assertFalse($bResult);
    }

    public function testUserUnmodifiableField()
    {
        $bResult = Field::unmodifiable('user', self::PUNCHLINE_FIELD_NAME);

        $this->assertTrue($bResult);
    }

    public function testAffiliateUnmodifiableField()
    {
        $bResult = Field::unmodifiable('aff', self::PHONE_FIELD_NAME);

        $this->assertTrue($bResult);
    }
}
