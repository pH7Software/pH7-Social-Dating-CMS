<?php
/* 
 * Phake - Mocking Framework
 * 
 * Copyright (c) 2010-2012, Mike Lively <m@digitalsandwich.com>
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 *  *  Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 * 
 *  *  Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 * 
 *  *  Neither the name of Mike Lively nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   Testing
 * @package    Phake
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.digitalsandwich.com/
 */

/**
 * Test the Phake Call Recorder
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_CallRecorder_RecorderTest extends PHPUnit_Framework_TestCase
{
    private $mock;

    public function setUp()
    {
        $this->mock = $this->getMock('Phake_IMock');
    }

    /**
     * Tests that the recorder can log a call and then pull that same call back out.
     */
    public function testRecord()
    {
        $call         = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $call2        = new Phake_CallRecorder_Call($this->mock, 'someMethod2', array());
        $callRecorder = new Phake_CallRecorder_Recorder();
        $callRecorder->recordCall($call);
        $callRecorder->recordCall($call2);

        $this->assertSame(array($call, $call2), $callRecorder->getAllCalls());
    }

    /**
     * Tests that the recorder can be rest to contain no calls.
     */
    public function testRemoveAllCalls()
    {
        $call         = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $call2        = new Phake_CallRecorder_Call($this->mock, 'someMethod2', array());
        $callRecorder = new Phake_CallRecorder_Recorder();
        $callRecorder->recordCall($call);
        $callRecorder->recordCall($call2);

        $callRecorder->removeAllCalls();

        $this->assertSame(array(), $callRecorder->getAllCalls());
    }

    /**
     * Tests retrieving call info for a particular call.
     */
    public function testRetrieveCallInfo()
    {
        $call         = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $callRecorder = new Phake_CallRecorder_Recorder();
        $callRecorder->recordCall($call);

        $callInfo = $callRecorder->getCallInfo($call);

        $this->assertInstanceOf('Phake_CallRecorder_CallInfo', $callInfo);
        $this->assertSame($call, $callInfo->getCall());
        $this->assertInstanceOf('Phake_CallRecorder_Position', $callInfo->getPosition());
    }

    /**
     * Tests that a non existant call returns null
     */
    public function testRetrieveCallInfoReturnsNull()
    {
        $call         = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $callRecorder = new Phake_CallRecorder_Recorder();

        $this->assertNull($callRecorder->getCallInfo($call));
    }

    /**
     * Tests an internal php nested object issue (#47)
     */
    public function testRetrieveCallInfoUsesStrictChecking()
    {
        $objA    = new stdClass();
        $objB    = new stdClass();
        $objA->b = $objB;
        $objB->a = $objA;

        $objC    = new stdClass();
        $objD    = new stdClass();
        $objC->b = $objD;
        $objD->a = $objC;

        $call         = new Phake_CallRecorder_Call($this->mock, 'someMethod', array($objA));
        $callRecorder = new Phake_CallRecorder_Recorder();
        $callRecorder->recordCall($call);

        $checkCall = new Phake_CallRecorder_Call($this->mock, 'someMethod', array($objC));

        $this->assertNull($callRecorder->getCallInfo($checkCall));
    }

    public function testMarkingCallsVerified()
    {
        $call1 = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $call2 = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());
        $call3 = new Phake_CallRecorder_Call($this->mock, 'someMethod', array());

        $callRecorder = new Phake_CallRecorder_Recorder();
        $callRecorder->recordCall($call1);
        $callRecorder->recordCall($call2);

        $callRecorder->markCallVerified($call2);

        $callRecorder->recordCall($call3);

        $this->assertEquals(array($call1, $call3), $callRecorder->getUnverifiedCalls());
    }
}
