<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\Api;
use PHPUnit_Framework_TestCase;

class ApiTest extends PHPUnit_Framework_TestCase
{
    use Api;

    public function testSetWithWrongDataType()
    {
        $this->assertFalse($this->set('wrong type'));
    }

    public function testSetWithValidData()
    {
        $aData = json_decode('{"status":1,"msg":"Hello World!"}', true);

        $this->assertSame(['status' => 1, 'msg' => 'Hello World!'], $aData);
    }
}
