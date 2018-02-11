<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Ajax
 */

namespace PH7\Test\Unit\Framework\Ajax;

use PH7\Framework\Ajax\Ajax;
use PHPUnit_Framework_TestCase;

class AjaxTest extends PHPUnit_Framework_TestCase
{
    public function testJsonMsg()
    {
        $sActualResult = Ajax::jsonMsg(1, 'Yaaay!');
        $this->assertSame('{"status":1,"txt":"Yaaay!"}', $sActualResult);
    }
}
