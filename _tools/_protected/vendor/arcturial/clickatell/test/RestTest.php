<?php
namespace Clickatell;

use \PHPUnit_Framework_TestCase;

class RestTest extends PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $rest = new \Clickatell\Rest('');
        $reflection = new \ReflectionClass($rest);
        $method = $reflection->getMethod('handle');
        $method->setAccessible(true);

        // Test accepted scenario
        $curlResult = '{"messages":[{"apiMessageId":null,"accepted":false,"to":"27111111111","error":"Invalid destination address."}],"error":null}';
        $result = $method->invokeArgs($rest, [$curlResult, 202]);

        $message = current($result['messages']);
        $this->assertSame($message['error'], 'Invalid destination address.');
        $this->assertSame($message['apiMsgId'], null);
        $this->assertSame($message['accepted'], false);

        // Test error scenario
        $curlResult = '{"messages":[],"error":"Some error occured"}';
        $this->setExpectedException(\Clickatell\ClickatellException::class, 'Some error occured');
        $result = $method->invokeArgs($rest, [$curlResult, 404]);
    }

    public function testParseStatusCallback()
    {
        $testFile = '/tmp/cv2input';
        $assert = false;

        $values = [
            'apiKey'            => 1,
            'messageId'         => 2,
            'requestId'         => 3,
            'clientMessageId'   => 4,
            'to'                => 5,
            'from'              => 6,
            'status'            => 7,
            'statusDescription' => 8,
            'timestamp'         => 9
        ];

        file_put_contents($testFile, json_encode($values));

        \Clickatell\Rest::parseStatusCallback(function ($result) use (&$assert, $values) {
            $this->assertSame($result, $values);
            $assert = true;
        }, $testFile);

        $this->assertTrue($assert, 'Callback not invoked.');
    }

    public function testParseReplyCallback()
    {
        $testFile = '/tmp/cv2input';
        $assert = false;

        $values = [
            'integrationId'     => 1,
            'messageId'         => 2,
            'replyMessageId'    => 3,
            'apiKey'            => 4,
            'fromNumber'        => 5,
            'toNumber'          => 6,
            'timestamp'         => 7,
            'text'              => 8,
            'charset'           => 9,
            'udh'               => 10,
            'network'           => 11,
            'keyword'           => 12
        ];

        file_put_contents($testFile, json_encode($values));

        \Clickatell\Rest::parseReplyCallback(function ($result) use (&$assert, $values) {
            $this->assertSame($result, $values);
            $assert = true;
        }, $testFile);

        $this->assertTrue($assert, 'Callback not invoked.');
    }
}