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
 * Description of VerifierTest
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_CallRecorder_VerifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_CallRecorder_Recorder
     */
    private $recorder;

    /**
     * @var Phake_CallRecorder_Verifier
     */
    private $verifier;

    /**
     * @var array
     */
    private $callArray;

    /**
     * @var Phake_CallRecorder_IVerifierMode
     */
    private $verifierMode;

    /**
     * @var Phake_IMock
     */
    private $obj;

    /**
     * Sets up the verifier and its call recorder
     */
    public function setUp()
    {
        $this->obj        = Phake::mock('Phake_IMock');

        $this->recorder     = Phake::mock('Phake_CallRecorder_Recorder');
        $this->verifierMode = Phake::mock('Phake_CallRecorder_IVerifierMode');

        $this->callArray = array(
            new Phake_CallRecorder_Call($this->obj, 'foo', array()),
            new Phake_CallRecorder_Call($this->obj, 'bar', array()),
            new Phake_CallRecorder_Call($this->obj, 'foo', array(
                'bar',
                'foo'
            )),
            new Phake_CallRecorder_Call($this->obj, 'foo', array()),
        );

        Phake::when($this->recorder)->getAllCalls()->thenReturn($this->callArray);

        $this->verifier = new Phake_CallRecorder_Verifier($this->recorder, $this->obj);
    }

    /**
     * Tests that a verifier can find a call that has been recorded.
     */
    public function testVerifierFindsCall()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'bar',
            null,
            $this->verifierMode
        );
        $return      = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo($this->callArray[1])->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );
        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(true, array($return)),
            $this->verifier->verifyCall($expectation)
        );
    }

    /**
     * Tests that a verifier will not find a call that has not been recorded.
     */
    public function testVerifierDoesNotFindCall()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'test',
            null,
            $this->verifierMode
        );
        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $result = $this->verifier->verifyCall($expectation)->getMatchedCalls();
        $this->assertTrue(is_array($result), 'verifyCall did not return an array');
        $this->assertTrue(empty($result), 'test call was found but should not have been');
    }

    /**
     * Tests that a verifier will not find a call that has been recorded with non matching parameters.
     */
    public function testVerifierDoesNotFindCallWithUnmatchedArguments()
    {
        $matcher1 = new Phake_Matchers_EqualsMatcher('test', new \SebastianBergmann\Comparator\Factory());
        $matcher2 = new Phake_Matchers_EqualsMatcher('test', new \SebastianBergmann\Comparator\Factory());
        $matcher1->setNextMatcher($matcher2);
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'foo',
            $matcher1,
            $this->verifierMode
        );
        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $result = $this->verifier->verifyCall($expectation)->getMatchedCalls();
        $this->assertTrue(empty($result));
    }

    /**
     * Tests that a verifier returns an array of call info objects when it finds a call that matches
     */
    public function testVerifierReturnsCallInfoForMatchedCalls()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'foo',
            null,
            $this->verifierMode
        );

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo(Phake::anyParameters())->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $this->verifier->verifyCall($expectation);

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(true, array($return, $return)),
            $this->verifier->verifyCall($expectation)
        );
    }


    /**
     * Tests that a verifier can find a call using AnyParameters matcher
     */
    public function testVerifierFindsCallWithAnyParameters()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'bar',
            new Phake_Matchers_AnyParameters(),
            $this->verifierMode
        );

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo($this->callArray[1])->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(true, array($return)),
            $this->verifier->verifyCall($expectation),
            'bar call was not found'
        );
    }

    /**
     * Tests that the verifier will only return calls made on the same object
     */
    public function testVerifierBeingCalledWithMixedCallRecorder()
    {
        $recorder = new Phake_CallRecorder_Recorder();
        $obj1     = $this->getMock('Phake_IMock');
        $obj2     = $this->getMock('Phake_IMock');

        $expectation = new Phake_CallRecorder_CallExpectation(
            $obj1,
            'foo',
            null,
            $this->verifierMode
        );

        $recorder->recordCall(new Phake_CallRecorder_Call($obj1, 'foo', array()));
        $recorder->recordCall(new Phake_CallRecorder_Call($obj2, 'foo', array()));

        $verifier = new Phake_CallRecorder_Verifier($recorder, $obj1);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $this->assertEquals(1, count($verifier->verifyCall($expectation)->getMatchedCalls()));
    }

    public function testVerifierChecksVerificationMode()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'foo',
            null,
            $this->verifierMode
        );

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo(Phake::anyParameters())->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $this->verifier->verifyCall($expectation);

        Phake::verify($this->verifierMode)->verify(Phake::capture($verifyCallInfo));
        $this->assertEquals(array($return, $return), $verifyCallInfo);
    }

    public function testVerifierReturnsFalseWhenAnExpectationIsNotMet()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'foo',
            null,
            $this->verifierMode
        );

        Phake::when($this->verifierMode)->__toString()->thenReturn('exactly 1 times');

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo(Phake::anyParameters())->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(false, 'actually called 0 times')
        );

        $expectedMessage = 'Expected Phake_IMock->foo() to be called exactly 1 times, actually called 0 times.
Other Invocations:
===
  Phake_IMock->foo(<string:bar>, <string:foo>)
  No matchers were given to Phake::when(), but arguments were received by this method.
===';

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(false, array(), $expectedMessage),
            $this->verifier->verifyCall($expectation)
        );
    }

    public function testVerifierModifiesFailureDescriptionIfThereAreNoInteractions()
    {
        $obj2        = Phake::mock('Phake_IMock');

        $expectation = new Phake_CallRecorder_CallExpectation(
            $obj2,
            'foo',
            null,
            $this->verifierMode
        );

        Phake::when($this->verifierMode)->__toString()->thenReturn('exactly 1 times');

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo(Phake::anyParameters())->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(false, 'actually called 0 times')
        );

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(false, array(), 'Expected Phake_IMock->foo() to be called exactly 1 times, actually called 0 times. In fact, there are no interactions with this mock.'),
            $this->verifier->verifyCall($expectation)
        );

        Phake::verify($this->verifierMode)->verify(array());
    }

    public function testVerifierModifiesFailureDescriptionWithOtherCalls()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'foo',
            new Phake_Matchers_EqualsMatcher('test', new \SebastianBergmann\Comparator\Factory()),
            $this->verifierMode
        );

        Phake::when($this->verifierMode)->__toString()->thenReturn('exactly 1 times');

        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo(Phake::anyParameters())->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(false, 'actually called 0 times')
        );

        $expected_msg =
            "Expected Phake_IMock->foo(equal to <string:test>) to be called exactly 1 times, actually called 0 times.\n"
                . "Other Invocations:\n"
                . "===\n"
                . "  Phake_IMock->foo()\n"
                . "  Argument #1 failed test\n"
                . "  Failed asserting that null matches expected 'test'.\n"
                . "===\n"
                . "  Phake_IMock->foo(<string:bar>, <string:foo>)\n"
                . "  Argument #1 failed test\n"
                . "  Failed asserting that two strings are equal.\n"
                . "  \n"
                . "  --- Expected\n"
                . "  +++ Actual\n"
                . "  @@ @@\n"
                . "  -'test'\n"
                . "  +'bar'\n"
                . "===\n"
                . "  Phake_IMock->foo()\n"
                . "  Argument #1 failed test\n"
                . "  Failed asserting that null matches expected 'test'.\n"
                . "===";

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(false, array(), $expected_msg),
            $this->verifier->verifyCall($expectation)
        );
    }

    public function testVerifyNoCalls()
    {
        Phake::when($this->recorder)->getAllCalls()->thenReturn(array());

        $this->assertEquals(new Phake_CallRecorder_VerifierResult(true, array()), $this->verifier->verifyNoCalls());
    }

    public function testVerifyNoCallsFailsWithOtherCallsListed()
    {
        $expected_msg =
            "Expected no interaction with mock\n"
                . "Invocations:\n"
                . "  Phake_IMock->foo()\n"
                . "  Phake_IMock->bar()\n"
                . "  Phake_IMock->foo(<string:bar>, <string:foo>)\n"
                . "  Phake_IMock->foo()";

        $this->assertEquals(
            new Phake_CallRecorder_VerifierResult(false, array(), $expected_msg),
            $this->verifier->verifyNoCalls()
        );
    }

    public function testVerifyMarksMatchedCallsAsVerified()
    {
        $expectation = new Phake_CallRecorder_CallExpectation(
            $this->obj,
            'bar',
            null,
            $this->verifierMode
        );
        $return = new Phake_CallRecorder_CallInfo($this->callArray[1], new Phake_CallRecorder_Position(0));
        Phake::when($this->recorder)->getCallInfo($this->callArray[1])->thenReturn($return);

        Phake::when($this->verifierMode)->verify(Phake::anyParameters())->thenReturn(
            new Phake_CallRecorder_VerifierMode_Result(true, '')
        );

        $this->verifier->verifyCall($expectation);
        Phake::verify($this->recorder)->markCallVerified($this->callArray[1]);
        Phake::verify($this->recorder)->markCallVerified(Phake::anyParameters());
    }

    public function testVerifyNoOtherCallsSucceeds()
    {
        Phake::when($this->recorder)->getUnverifiedCalls()->thenReturn($this->callArray);
        $verifierResult = $this->verifier->verifyNoOtherCalls();

        $this->assertFalse($verifierResult->getVerified());
        $expected_msg =
            "Expected no interaction with mock\n"
            . "Invocations:\n"
            . "  Phake_IMock->foo()\n"
            . "  Phake_IMock->bar()\n"
            . "  Phake_IMock->foo(<string:bar>, <string:foo>)\n"
            . "  Phake_IMock->foo()";

        $this->assertEquals($expected_msg, $verifierResult->getFailureDescription());
        $this->assertEmpty($verifierResult->getMatchedCalls());
    }

    public function testVerifyNoOtherCallsFails()
    {
        Phake::when($this->recorder)->getUnverifiedCalls()->thenReturn($this->callArray);
        $verifierResult = $this->verifier->verifyNoOtherCalls();

        $this->assertFalse($verifierResult->getVerified());
        $expected_msg =
            "Expected no interaction with mock\n"
            . "Invocations:\n"
            . "  Phake_IMock->foo()\n"
            . "  Phake_IMock->bar()\n"
            . "  Phake_IMock->foo(<string:bar>, <string:foo>)\n"
            . "  Phake_IMock->foo()";

        $this->assertEquals($expected_msg, $verifierResult->getFailureDescription());
        $this->assertEmpty($verifierResult->getMatchedCalls());
    }
}


