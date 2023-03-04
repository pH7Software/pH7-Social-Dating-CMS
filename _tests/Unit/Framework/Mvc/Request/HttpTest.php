<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Request
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Request;

use PH7\Datatype\Type;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Request\WrongRequestMethodException;
use PHPUnit\Framework\TestCase;

final class HttpTest extends TestCase
{
    private HttpRequest $oHttpRequest;

    protected function setUp(): void
    {
        $this->oHttpRequest = new HttpRequest;
    }

    public function testGetRequestCastedToInt(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['string_id'] = '123';

        $sActual = $this->oHttpRequest->get('string_id', Type::INTEGER);

        $this->assertSame(123, $sActual);
    }

    public function testGetRequestCastedToFloat(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['float_value'] = '10.3';

        $sActual = $this->oHttpRequest->get('float_value', Type::FLOAT);

        $this->assertSame(10.3, $sActual);
    }

    public function testGetRequestCastedToArray(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['array_value'] = '';

        $sActual = $this->oHttpRequest->get('array_value', Type::ARRAY);

        $this->assertIsArray($sActual);
    }

    public function testGetRequestCastedToBool(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['boolean_key'] = 'true';

        $sActual = $this->oHttpRequest->get('boolean_key', Type::BOOLEAN);

        $this->assertTrue($sActual);
    }

    public function testGetExistsWithWrongValidateType(): void
    {
        $_GET['id'] = 123;

        $bActual = $this->oHttpRequest->getExists('id', Type::STRING);

        $this->assertFalse($bActual);
    }

    public function testGetExistsWithValidType(): void
    {
        $_GET['id'] = 0;

        $bActual = $this->oHttpRequest->getExists('id', Type::INTEGER);

        $this->assertTrue($bActual);
    }

    public function testGetNotExists(): void
    {
        $bActual = $this->oHttpRequest->getExists('undefined_key');

        $this->assertFalse($bActual);
    }

    public function testGetExistsWithSeveralKeys(): void
    {
        $_GET['key1'] = 'blabla';
        $_GET['key2'] = 'blabla';

        $bActual = $this->oHttpRequest->getExists(['key1', 'key2']);

        $this->assertTrue($bActual);
    }

    public function testGetRequestWithGets(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['name'] = 'value';

        $sActual = $this->oHttpRequest->gets('name');

        $this->assertSame('value', $sActual);
    }

    public function testPostRequestCastedToInt(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['string_id'] = '123';

        $sActual = $this->oHttpRequest->post('string_id', Type::INTEGER);

        $this->assertSame(123, $sActual);
    }

    public function testPostRequestCastedToFloat(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['float_value'] = '10.3';

        $sActual = $this->oHttpRequest->post('float_value', Type::FLOAT);

        $this->assertSame(10.3, $sActual);
    }

    public function testPostRequestCastedToArray(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['array_value'] = '';

        $sActual = $this->oHttpRequest->post('array_value', Type::ARRAY);

        $this->assertIsArray($sActual);
    }

    public function testPostRequestCastedToBool(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['boolean_key'] = 0;

        $sActual = $this->oHttpRequest->post('boolean_key', Type::BOOLEAN);

        $this->assertFalse($sActual);
    }

    public function testPostExistsWithWrongValidateType(): void
    {
        $_POST['id'] = 123;

        $bActual = $this->oHttpRequest->postExists('id', Type::STRING);

        $this->assertFalse($bActual);
    }

    public function testPostExistsWithValidType(): void
    {
        $_POST['id'] = 0;

        $bActual = $this->oHttpRequest->postExists('id', Type::INTEGER);

        $this->assertTrue($bActual);
    }

    public function testPostNotExists(): void
    {
        $bActual = $this->oHttpRequest->postExists('undefined_key');

        $this->assertFalse($bActual);
    }

    public function testPostExistsWithSeveralKeys(): void
    {
        $_POST['key1'] = 'blabla';
        $_POST['key2'] = 'blabla';

        $bActual = $this->oHttpRequest->postExists(['key1', 'key2']);

        $this->assertTrue($bActual);
    }

    public function testPostRequestWithGets(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['name'] = 'value';

        $sActual = $this->oHttpRequest->gets('name');

        $this->assertSame('value', $sActual);
    }

    public function testPostMethodWithWrongRequestMethod(): void
    {
        $this->expectException(WrongRequestMethodException::class);
        $this->expectExceptionCode(WrongRequestMethodException::POST_METHOD);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST['foo'] = 'bar';

        $this->oHttpRequest->post('foo');
    }

    public function testSets(): void
    {
        $this->oHttpRequest->sets('setname', 'Pierre');

        $this->assertSame('Pierre', $this->oHttpRequest->get('setname'));
        $this->assertSame('Pierre', $this->oHttpRequest->post('setname'));
    }
}
