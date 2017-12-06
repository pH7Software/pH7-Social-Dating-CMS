<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Request
 */

namespace PH7\Test\Unit\Framework\Mvc\Request;

use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PHPUnit_Framework_TestCase;

class HttpTest extends PHPUnit_Framework_TestCase
{
    /** @var HttpRequest */
    private $oHttpRequest;

    protected function setUp()
    {
        $this->oHttpRequest = new HttpRequest;
    }

    public function testGetRequestWithIntCast()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['string_id'] = '123';

        $sActual = $this->oHttpRequest->get('string_id', 'int');

        $this->assertSame(123, $sActual);
    }

    public function testPostRequestWithIntCast()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['string_id'] = '123';

        $sActual = $this->oHttpRequest->post('string_id', 'int');

        $this->assertSame(123, $sActual);
    }

    protected function tearDown()
    {
        unset($_GET, $_POST, $_SERVER);
    }
}

