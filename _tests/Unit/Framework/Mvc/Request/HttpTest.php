<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Request
 */

namespace PH7\Test\Unit\Framework\Mvc\Request;

use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Request\WrongRequestMethodException;
use PHPUnit_Framework_TestCase;

class HttpTest extends PHPUnit_Framework_TestCase
{
    /** @var HttpRequest */
    private $oHttpRequest;

    protected function setUp()
    {
        $this->oHttpRequest = new HttpRequest;
    }

    public function testGetRequestCastedToInt()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['string_id'] = '123';

        $sActual = $this->oHttpRequest->get('string_id', 'int');

        $this->assertSame(123, $sActual);
    }

    public function testGetRequestCastedToFloat()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['float_value'] = '10.3';

        $sActual = $this->oHttpRequest->get('float_value', 'float');

        $this->assertSame(10.3, $sActual);
    }

    public function testGetRequestCastedToBool()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['boolean_key'] = 'true';

        $sActual = $this->oHttpRequest->get('boolean_key', 'bool');

        $this->assertTrue($sActual);
    }

    public function testGetExistsWithWrongValidateType()
    {
        $_GET['id'] = 123;

        $bActual = $this->oHttpRequest->getExists('id', 'string');

        $this->assertFalse($bActual);
    }

    public function testGetExistsWithValidType()
    {
        $_GET['id'] = 0;

        $bActual = $this->oHttpRequest->getExists('id', 'int');

        $this->assertTrue($bActual);
    }

    public function testGetNotExists()
    {
        $bActual = $this->oHttpRequest->getExists('undefined_key');

        $this->assertFalse($bActual);
    }

    public function testGetExistsWithSeveralKeys()
    {
        $_GET['key1'] = 'blabla';
        $_GET['key2'] = 'blabla';

        $bActual = $this->oHttpRequest->getExists(['key1', 'key2']);

        $this->assertTrue($bActual);
    }

    public function testGetRequestWithGets()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['name'] = 'value';

        $sActual = $this->oHttpRequest->gets('name');

        $this->assertSame('value', $sActual);
    }

    public function testPostRequestCastedToInt()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['string_id'] = '123';

        $sActual = $this->oHttpRequest->post('string_id', 'int');

        $this->assertSame(123, $sActual);
    }

    public function testPostRequestCastedToFloat()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['float_value'] = '10.3';

        $sActual = $this->oHttpRequest->post('float_value', 'float');

        $this->assertSame(10.3, $sActual);
    }

    public function testPostRequestCastedToBool()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['boolean_key'] = 0;

        $sActual = $this->oHttpRequest->post('boolean_key', 'bool');

        $this->assertFalse($sActual);
    }

    public function testPostExistsWithWrongValidateType()
    {
        $_POST['id'] = 123;

        $bActual = $this->oHttpRequest->postExists('id', 'string');

        $this->assertFalse($bActual);
    }

    public function testPostExistsWithValidType()
    {
        $_POST['id'] = 0;

        $bActual = $this->oHttpRequest->postExists('id', 'int');

        $this->assertTrue($bActual);
    }

    public function testPostNotExists()
    {
        $bActual = $this->oHttpRequest->postExists('undefined_key');

        $this->assertFalse($bActual);
    }

    public function testPostExistsWithSeveralKeys()
    {
        $_POST['key1'] = 'blabla';
        $_POST['key2'] = 'blabla';

        $bActual = $this->oHttpRequest->postExists(['key1', 'key2']);

        $this->assertTrue($bActual);
    }

    public function testPostRequestWithGets()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['name'] = 'value';

        $sActual = $this->oHttpRequest->gets('name');

        $this->assertSame('value', $sActual);
    }

    public function testPostMethodWithWrongRequestMethod()
    {
        $this->expectException(WrongRequestMethodException::class);
        $this->expectExceptionCode(WrongRequestMethodException::POST_METHOD);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST['foo'] = 'bar';

        $this->oHttpRequest->post('foo');
    }

    public function testSets()
    {
        $this->oHttpRequest->sets('setname', 'Pierre');

        $this->assertSame('Pierre', $this->oHttpRequest->get('setname'));
        $this->assertSame('Pierre', $this->oHttpRequest->post('setname'));
    }
}

