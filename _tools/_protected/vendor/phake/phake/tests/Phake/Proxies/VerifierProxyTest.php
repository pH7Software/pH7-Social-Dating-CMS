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
 * Description of VerifierProxyTest
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Proxies_VerifierProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_CallRecorder_Verifier
     */
    private $verifier;

    /**
     * @var Phake_Proxies_VerifierProxy
     */
    private $proxy;

    /**
     * @var Phake_Client_IClient
     */
    private $client;

    /**
     * @var array
     */
    private $matchedCalls;

    public function setUp()
    {
        $this->verifier = Phake::mock('Phake_CallRecorder_Verifier');
        $this->mode = Phake::mock('Phake_CallRecorder_IVerifierMode');
        $this->client = Phake::mock('Phake_Client_IClient');
        $this->matchedCalls = array(
            Phake::mock('Phake_CallRecorder_CallInfo'),
            Phake::mock('Phake_CallRecorder_CallInfo'),
        );

        $this->proxy = new Phake_Proxies_VerifierProxy($this->verifier, new Phake_Matchers_Factory(), $this->mode, $this->client);
        $obj         = $this->getMock('Phake_IMock');
        Phake::when($this->verifier)->getObject()->thenReturn($obj);
        Phake::when($this->mode)->__toString()->thenReturn('exactly 1 times');
        Phake::when($this->client)->processVerifierResult($this->anything())->thenReturn($this->matchedCalls);
    }

    /**
     * Tests that the proxy will call the verifier with the method properly forwarded
     */
    public function testVerifierCallsAreForwardedMethod()
    {
        Phake::when($this->verifier)->verifyCall(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierResult(true, array(Phake::mock('Phake_CallRecorder_CallInfo')))
        );
        $this->proxy->foo();

        Phake::verify($this->verifier)->verifyCall(Phake::capture($expectation));
        $this->assertEquals('foo', $expectation->getMethod());
    }

    /**
     * Tests that call information from the proxied verifier is returned
     */
    public function testVerifierReturnsCallInfoData()
    {
        Phake::when($this->verifier)->verifyCall(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierResult(true, $this->matchedCalls)
        );

        $this->assertSame($this->matchedCalls, $this->proxy->foo());
    }

    /**
     * Tests that verifier calls will forward method arguments properly
     */
    public function testVerifierCallsAreForwardedArguments()
    {
        $argumentMatcher = Phake::mock('Phake_Matchers_IChainableArgumentMatcher');

        Phake::when($this->verifier)->verifyCall(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierResult(true, array(Phake::mock('Phake_CallRecorder_CallInfo')))
        );
        $this->proxy->foo($argumentMatcher);

        Phake::verify($this->verifier)->verifyCall(Phake::capture($expectation));
        $this->assertEquals($argumentMatcher, $expectation->getArgumentMatcher());
    }

    /**
     * Tests that verifier calls that are not given an argument matcher will generate an equals matcher
     * with the given value.
     */
    public function testProxyTransformsNonMatchersToEqualsMatcher()
    {
        $argumentMatcher = new Phake_Matchers_EqualsMatcher('test', new \SebastianBergmann\Comparator\Factory());
        Phake::when($this->verifier)->verifyCall(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierResult(true, array(Phake::mock('Phake_CallRecorder_CallInfo')))
        );
        $this->proxy->foo('test');

        Phake::verify($this->verifier)->verifyCall(Phake::capture($expectation));
        $this->assertEquals($argumentMatcher, $expectation->getArgumentMatcher());
    }

    public function testClientResultProcessorIsCalled()
    {
        $result = new Phake_CallRecorder_VerifierResult(true, $this->matchedCalls);
        Phake::when($this->verifier)->verifyCall(Phake::anyParameters())->thenReturn($result);

        $this->proxy->foo();

        Phake::verify($this->client)->processVerifierResult($result);
    }

    /**
     * @dataProvider magicGetInvalidData
     */
    public function testMagicGetWithInvalidData($invalidData, $exceptionContains)
    {
        $this->setExpectedException('InvalidArgumentException', $exceptionContains);
        $this->proxy->__get($invalidData);
    }

    public function magicGetInvalidData()
    {
        return array(
            array('1foo', 'cannot start with an integer'),
            array(1,      'must be a string'),
        );
    }
}


