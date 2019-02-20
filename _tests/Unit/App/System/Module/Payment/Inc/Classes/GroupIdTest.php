<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Payment / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Payment\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'payment/inc/class/GroupId.php';

use PH7\GroupId;
use PHPUnit_Framework_TestCase;

class GroupIdTest extends PHPUnit_Framework_TestCase
{
    const VISITOR_GROUP = 1;
    const PENDING_GROUP = 9;
    const DEFAULT_MEMBERSHIP_ID = 2;
    const CUSTOM_GROUP = 22;

    public function testUndeletableWithVisitorGroup()
    {
        $bResult = GroupId::undeletable(
            self::VISITOR_GROUP,
            self::DEFAULT_MEMBERSHIP_ID
        );

        $this->assertTrue($bResult);
    }

    public function testUndeletableWithPendingGroup()
    {
        $bResult = GroupId::undeletable(
            self::PENDING_GROUP,
            self::DEFAULT_MEMBERSHIP_ID
        );

        $this->assertTrue($bResult);
    }

    public function testUndeletableWithDefaultGroup()
    {
        $bResult = GroupId::undeletable(
            self::DEFAULT_MEMBERSHIP_ID,
            self::DEFAULT_MEMBERSHIP_ID
        );

        $this->assertTrue($bResult);
    }

    public function testUndeletableWithCustomGroup()
    {
        $bResult = GroupId::undeletable(
            self::CUSTOM_GROUP,
            self::DEFAULT_MEMBERSHIP_ID
        );

        $this->assertFalse($bResult);
    }
}
