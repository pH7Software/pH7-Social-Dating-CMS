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
 * Tests the behavior of the Phake class.
 *
 * The tests below are really all integration tests.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class PhakeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Phake::setClient(Phake::CLIENT_DEFAULT);
    }

    protected function tearDown()
    {
        Phake::resetStaticInfo();
        Phake::setClient(Phake::CLIENT_DEFAULT);
    }

    /**
     * General test for Phake::mock() that it returns a class that inherits from the passed class.
     */
    public function testMock()
    {
        $this->assertThat(Phake::mock('stdClass'), $this->isInstanceOf('stdClass'));
    }

    /**
     * Tests that a simple method call can be verified
     */
    public function testSimpleVerifyPasses()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();

        Phake::verify($mock)->foo();
    }

    /**
     * Tests that a simple method call verification with throw an exception if that method was not
     * called.
     *
     * @expectedException Phake_Exception_VerificationException
     */
    public function testSimpleVerifyThrowsExceptionOnFail()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::verify($mock)->foo();
    }

    /**
     * Tests that a simple method call can be stubbed to return an expected value.
     */
    public function testSimpleStub()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo()
            ->thenReturn(42);

        $this->assertEquals(42, $mock->foo());
    }

    public function testStaticStub()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        Phake::whenStatic($mock)->staticMethod()->thenReturn(42);

        $this->assertEquals(42, $mock::staticMethod());
    }

    /**
     * Tests default parameters
     */
    public function testStubWithDefaultParam()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithDefault()
            ->thenReturn(42);

        $this->assertEquals(42, $mock->fooWithDefault());
    }

    /**
     * Tests that a stub can be redefined.
     */
    public function testRedefineStub()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo()->thenReturn(24);
        Phake::when($mock)->foo()->thenReturn(42);

        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests that a stub method can be defined with shorthand notation.
     */
    public function testShorthandVerify()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo();
        $mock->foo('bar');

        Phake::verify($mock, Phake::times(2))->foo;
    }

    /**
     * Tests that a stub method can be defined with shorthand notation.
     */
    public function testShorthandStub()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo->thenReturn(42);

        $this->assertEquals(42, $mock->foo());
        $this->assertEquals(42, $mock->foo('param'));
    }

    /**
     * Tests that a stub method can be defined with shorthand notation later.
     */
    public function testFirstShorthandStub()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo->thenReturn(42);
        Phake::when($mock)->foo('param')->thenReturn(51);

        $this->assertEquals(51, $mock->foo('param'));
        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests that a stub method can be redefined with shorthand notation.
     */
    public function testRedefinedShorthandStub()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo->thenReturn(42);
        Phake::when($mock)->foo->thenReturn(2);

        $this->assertEquals(2, $mock->foo());
    }

    /**
     * Tests that a stub method can be defined with shorthand notation even with __get().
     */
    public function testMagicClassShorthandStub()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::when($mock)->definedMethod->thenReturn(64);
        Phake::when($mock)->__get->thenReturn(75);
        Phake::when($mock)->magicProperty->thenReturn(42);

        $this->assertSame(64, $mock->definedMethod());
        $this->assertSame(75, $mock->otherMagicProperties);
        $this->assertSame(42, $mock->magicProperty);
    }

    /**
     * Tests using multiple stubs.
     */
    public function testMultipleStubs()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo()->thenReturn(24);
        Phake::when($mock)->fooWithReturnValue()->thenReturn(42);

        $this->assertEquals(24, $mock->foo());
        $this->assertEquals(42, $mock->fooWithReturnValue());
    }

    /**
     * Tests using multiple stubs.
     */
    public function testConsecutiveCalls()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo()->thenReturn(24)->thenReturn(42);

        $this->assertEquals(24, $mock->foo());
        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests passing a basic equals matcher to the verify method will correctly verify a call.
     */
    public function testVerifyCallWithEqualsMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('bar');

        Phake::verify($mock)->fooWithArgument(Phake::equalTo('bar'));
    }

    /**
     * Tests passing a basic equals matcher to the verify method will correctly fail when matcher is not satisfied.
     *
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyCallWithEqualsMatcherFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('test');

        Phake::verify($mock)->fooWithArgument(Phake::equalTo('bar'));
    }

    /**
     * Tests that we can implicitely indicate an equalTo matcher when we pass in a non-matcher value.
     */
    public function testVerifyCallWithDefaultMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('bar');

        Phake::verify($mock)->fooWithArgument('bar');
    }

    /**
     * Tests passing a default matcher type to the verify method will correctly fail when matcher is not satisfied.
     *
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyCallWithDefaultMatcherFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('test');

        Phake::verify($mock)->fooWithArgument('bar');
    }

    /**
     * Tests passing in a PHPUnit constraint to the verifier
     */
    public function testVerifyCallWithPHPUnitMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('bar');

        Phake::verify($mock)->fooWithArgument($this->equalTo('bar'));
    }

    /**
     * Tests passing in a PHPUnit constraint to the verifier fails when constraint not met.
     *
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyCallWithPHPUnitMatcherFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('test');

        Phake::verify($mock)->fooWithArgument($this->equalTo('bar'));
    }

    /**
     * Tests passing in a Hamcrest matcher to the verifier
     */
    public function testVerifyCallWithHamcrestMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('bar');

        Phake::verify($mock)->fooWithArgument(equalTo('bar'));
    }

    /**
     * Tests passing in a Hamcrest matcher to the verifier fails when constraint not met.
     *
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyCallWithHamcrestMatcherFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('test');

        Phake::verify($mock)->fooWithArgument(equalTo('bar'));
    }

    /**
     * Tests using an equalTo argument matcher with a method stub
     */
    public function testStubWithEqualsMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithArgument(Phake::equalTo('bar'))->thenReturn(42);

        $this->assertEquals(42, $mock->fooWithArgument('bar'));
        $this->assertNull($mock->fooWithArgument('test'));
    }

    /**
     * Tests using an implicit equalTo argument matcher with a method stub
     */
    public function testStubWithDefaultMatcher()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithArgument('bar')->thenReturn(42);

        $this->assertEquals(42, $mock->fooWithArgument('bar'));
        $this->assertNull($mock->fooWithArgument('test'));
    }

    /**
     * Tests using a phpunit constraint with a method stub
     */
    public function testStubWithPHPUnitConstraint()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithArgument($this->equalTo('bar'))->thenReturn(42);

        $this->assertEquals(42, $mock->fooWithArgument('bar'));
        $this->assertNull($mock->fooWithArgument('test'));
    }

    /**
     * Tests using a hamcrest matcher with a method stub
     */
    public function testStubWithHamcrestConstraint()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithArgument(equalTo('bar'))->thenReturn(42);

        $this->assertEquals(42, $mock->fooWithArgument('bar'));
        $this->assertNull($mock->fooWithArgument('test'));
    }

    /**
     * Tests that resetting a mock clears the call recorder
     */
    public function testResettingCallRecorder()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();

        Phake::verify($mock)->foo();

        Phake::reset($mock);

        $this->setExpectedException('Phake_Exception_VerificationException');

        Phake::verify($mock)->foo();
    }

    /**
     * Tests that resetting a mock clears the stubber
     */
    public function testResettingStubMapper()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo()->thenReturn(42);

        $this->assertEquals(42, $mock->foo());

        Phake::reset($mock);

        $this->assertNull($mock->foo());
    }

    /**
     * Tests that resetting a mock clears the call recorder
     */
    public function testResettingStaticCallRecorder()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        $mock::staticMethod();

        Phake::verifyStatic($mock)->staticMethod();

        Phake::resetStatic($mock);

        $this->setExpectedException('Phake_Exception_VerificationException');

        Phake::verifyStatic($mock)->staticMethod();
    }

	public function testMockingPhar()
	{
		if (!class_exists('Phar'))
		{
			$this->markTestSkipped('Phar class does not exist');
		}
		$phar = Phake::mock('Phar');

		$this->assertInstanceOf('Phar', $phar);
	}

    /**
     * Tests that resetting a mock clears the stubber
     */
    public function testResettingStaticStubMapper()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        Phake::whenStatic($mock)->staticMethod()->thenReturn(42);

        $this->assertEquals(42, $mock::staticMethod());

        Phake::resetStatic($mock);

        $this->assertNull($mock::staticMethod());
    }

    /**
     * Tests setting a default answer for stubs
     */
    public function testDefaultAnswerForStubs()
    {
        $mock = Phake::mock('PhakeTest_MockedClass', Phake::ifUnstubbed()->thenReturn(42));

        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests setting a default answer for stubs
     */
    public function testDefaultAnswerForInterfaces()
    {
        $mock = Phake::mock('PhakeTest_MockedInterface', Phake::ifUnstubbed()->thenReturn(42));

        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests setting a default answer for only the __call magic method
     */
    public function testDefaultAnswerForStubsOfCall()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::whenCallMethodWith(Phake::anyParameters())->isCalledOn($mock)->thenReturn(42);

        $this->assertEquals(42, $mock->foo());
    }

    /**
     * Tests setting a default answer for only the __call magic method
     */
    public function testDefaultAnswerForStaticStubsOfCall()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::whenStaticCallMethodWith(Phake::anyParameters())->isCalledOn($mock)->thenReturn(42);

        $this->assertEquals(42, $mock::foo());
    }

    /**
     * Tests validating calls to __call
     */
    public function testVerificationOfCall()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        $mock->foo();

        Phake::verifyCallMethodWith(Phake::anyParameters())->isCalledOn($mock);
    }

    /**
     * Tests validating calls to __callStatic
     */
    public function testVerificationOfStaticCall()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        $mock::foo();

        Phake::verifyStaticCallMethodWith(Phake::anyParameters())->isCalledOn($mock);
    }

    /**
     * Tests stubbing a mocked method to call its parent.
     */
    public function testStubbingMethodToCallParent()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithReturnValue()->thenCallParent();

        $this->assertEquals('blah', $mock->fooWithReturnValue());
    }

    /**
     * Tests calling through a chain of calls
     */
    public function testStubbingChainedMethodsToCallParent()
    {
        $mock = Phake::mock('PhakeTest_MockedClass', Phake::ifUnstubbed()->thenCallParent());

        $this->assertEquals('test', $mock->callInnerFunc());
    }

    /**
     * Tests partial mock functionality to make sure original method is called.
     */
    public function testPartialMockCallsOriginal()
    {
        $pmock = Phake::partialMock('PhakeTest_MockedClass');
        $this->assertEquals('blah', $pmock->fooWithReturnValue());
    }

    /**
     * Tests partial mock calls are recorded
     */
    public function testPartialMockRecordsCall()
    {
        $pmock = Phake::partialMock('PhakeTest_MockedClass');
        $pmock->foo();

        Phake::verify($pmock)->foo();
    }

    /**
     * Tests that partial mock calls can chain properly
     */
    public function testPartialMockInternalMethodCalls()
    {
        $pmock = Phake::partialMock('PhakeTest_MockedClass');
        Phake::when($pmock)->innerFunc()->thenReturn('blah');

        $this->assertEquals('blah', $pmock->chainedCall());
    }

    /**
     * Tests that partial mock can overwrite methods
     * so that they don't do anything when they get called
     */
    public function testPartialMockCanReturnNothing()
    {
        $pmock = Phake::partialMock('PhakeTest_MockedClass');
        Phake::when($pmock)->innerFunc()->thenDoNothing();

        $this->assertNull($pmock->chainedCall());
    }

    /**
     * Tests that partial mocks listen to the constructor args given
     */
    public function testPartialMockCallsConstructor()
    {
        $pmock = Phake::partialMock('PhakeTest_MockedConstructedClass', 'val1', 'val2', 'val3');

        $this->assertEquals('val1', $pmock->getProp1());
        $this->assertEquals('val2', $pmock->getProp2());
        $this->assertEquals('val3', $pmock->getProp3());
    }

    /**
     * Tests that partial mocks with constructors higher in the chain have their constructors called
     */
    public function testPartialMockCallsParentConstructor()
    {
        $pmock = Phake::partialMock('PhakeTest_ExtendedMockedConstructedClass', 'val1', 'val2', 'val3');

        $this->assertEquals('val1', $pmock->getProp1());
        $this->assertEquals('val2', $pmock->getProp2());
        $this->assertEquals('val3', $pmock->getProp3());
    }

    /**
     * Tests that the deprecated partMock works
     */
    public function testPartMock()
    {
        $pmock = Phake::partMock('PhakeTest_ExtendedMockedConstructedClass', 'val1', 'val2', 'val3');

        $this->assertEquals('val1', $pmock->getProp1());
        $this->assertEquals('val2', $pmock->getProp2());
        $this->assertEquals('val3', $pmock->getProp3());
    }

    /**
     * Tests mocking of an interface
     */
    public function testMockingInterface()
    {
        $mock = Phake::mock('PhakeTest_MockedInterface');

        Phake::when($mock)->foo()->thenReturn('bar');

        $this->assertEquals('bar', $mock->foo());
    }

    /**
     * Tests mocking of an abstract class
     */
    public function testMockingAbstract()
    {
        $mock = Phake::mock('PhakeTest_AbstractClass');

        Phake::when($mock)->foo()->thenReturn('bar');

        $this->assertEquals('bar', $mock->foo());
    }

    /**
     * Tests verifying the call order of particular methods within an object
     */
    public function testCallOrderInObject()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->fooWithReturnValue();
        $mock->callInnerFunc();

        Phake::inOrder(
            Phake::verify($mock)->foo(),
            Phake::verify($mock)->fooWithReturnValue(),
            Phake::verify($mock)->callInnerFunc()
        );
    }

    /**
     * Tests verifying the call order of particular methods within an object
     */
    public function testCallOrderInObjectFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->callInnerFunc();
        $mock->fooWithReturnValue();

        $this->setExpectedException('Phake_Exception_VerificationException');

        Phake::inOrder(
            Phake::verify($mock)->foo(),
            Phake::verify($mock)->fooWithReturnValue(),
            Phake::verify($mock)->callInnerFunc()
        );
    }

    /**
     * Tests verifying the call order of particular methods across objects
     */
    public function testCallOrderAccrossObjects()
    {
        $mock1 = Phake::mock('PhakeTest_MockedClass');
        $mock2 = Phake::mock('PhakeTest_MockedClass');

        $mock1->foo();
        $mock2->foo();
        $mock1->fooWithReturnValue();
        $mock2->fooWithReturnValue();
        $mock1->callInnerFunc();
        $mock2->callInnerFunc();

        Phake::inOrder(
            Phake::verify($mock1)->foo(),
            Phake::verify($mock2)->foo(),
            Phake::verify($mock2)->fooWithReturnValue(),
            Phake::verify($mock1)->callInnerFunc()
        );
    }

    /**
     * Tests verifying the call order of particular methods across objects
     */
    public function testCallOrderAccrossObjectsFail()
    {
        $mock1 = Phake::mock('PhakeTest_MockedClass');
        $mock2 = Phake::mock('PhakeTest_MockedClass');

        $mock1->foo();
        $mock2->foo();
        $mock1->fooWithReturnValue();
        $mock1->callInnerFunc();
        $mock2->fooWithReturnValue();
        $mock2->callInnerFunc();

        $this->setExpectedException('Phake_Exception_VerificationException');

        Phake::inOrder(
            Phake::verify($mock2)->fooWithReturnValue(),
            Phake::verify($mock1)->callInnerFunc()
        );
    }

    public function testCallOrderWithStatics()
    {
        $mock1 = Phake::mock('PhakeTest_MockedClass');
        $mock2 = Phake::mock('PhakeTest_StaticInterface');

        $mock1->foo();
        $mock2::staticMethod();
        $mock1->fooWithReturnValue();
        $mock1->callInnerFunc();

        Phake::inOrder(
            Phake::verify($mock1)->foo(),
            Phake::verifyStatic($mock2)->staticMethod(),
            Phake::verify($mock1)->callInnerFunc()
        );
    }

    /**
     * Tests freezing mocks
     */
    public function testMockFreezing()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();

        Phake::verifyNoFurtherInteraction($mock);

        $this->setExpectedException('Phake_Exception_VerificationException');

        $mock->foo();
    }

    public function testStaticMockFreezing()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        $mock::staticMethod();

        Phake::verifyNoFurtherInteraction($mock);

        $this->setExpectedException('Phake_Exception_VerificationException');

        $mock::staticMethod();
    }

    /**
     * Tests freezing mocks
     */
    public function testMockFreezingWithMultipleMocks()
    {
        $mock1 = Phake::mock('PhakeTest_MockedClass');
        $mock2 = Phake::mock('PhakeTest_MockedClass');

        $mock1->foo();
        $mock2->foo();

        Phake::verifyNoFurtherInteraction($mock1, $mock2);

        $this->setExpectedException('Phake_Exception_VerificationException');

        $mock2->foo();
    }

    /**
     * Tests verifying that no interaction occured
     */
    public function testVerifyingZeroInteraction()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::verifyNoInteraction($mock);

        $mock->foo();

        $this->setExpectedException('Phake_Exception_VerificationException');
        Phake::verifyNoInteraction($mock);
    }

    /**
     * Tests verifying that no interaction occured
     */
    public function testVerifyingZeroInteractionIncludesStatic()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        Phake::verifyNoInteraction($mock);

        $mock::staticMethod();

        $this->setExpectedException('Phake_Exception_VerificationException');
        Phake::verifyNoInteraction($mock);
    }

    /**
     * Tests verifying that no interaction occured
     */
    public function testVerifyingZeroInteractionWithMultipleArgs()
    {
        $mock1 = Phake::mock('PhakeTest_MockedClass');
        $mock2 = Phake::mock('PhakeTest_MockedClass');

        Phake::verifyNoInteraction($mock1, $mock2);

        $mock2->foo();

        $this->setExpectedException('Phake_Exception_VerificationException');
        Phake::verifyNoInteraction($mock1, $mock2);
    }

    /**
     * Tests argument capturing
     */
    public function testArugmentCapturing()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('TEST');

        Phake::verify($mock)->fooWithArgument(Phake::capture($toArgument));

        $this->assertSame('TEST', $toArgument);
    }

    /**
     * Tests conditional argument capturing
     */
    public function testConditionalArugmentCapturing()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('FOO');

        $mock->fooWithArgument('BAR');


        Phake::verify($mock)->fooWithArgument(Phake::capture($toArgument)->when('BAR'));

        $this->assertSame('BAR', $toArgument);
    }

    /**
     * Make sure arguments aren't captured if the conditions don't match
     */
    public function testConditionalArugmentCapturingFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithArgument('FOO');

        $this->setExpectedException('Phake_Exception_VerificationException');
        Phake::verify($mock)->fooWithArgument(Phake::capture($toArgument)->when('BAR'));
    }

    /**
     * Make sure arguments are captured with no issues
     */
    public function testArgumentCapturingWorksOnObjects()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $obj = new stdClass;

        $mock->fooWithArgument($obj);

        Phake::verify($mock)->fooWithArgument(Phake::capture($toArgument));

        $this->assertSame($obj, $toArgument);
    }

    /**
     * Make sure arguments are captured with no issues
     */
    public function testArgumentCapturingWorksOnStubbing()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $obj = new stdClass;

        Phake::when($mock)->fooWithArgument(Phake::capture($toArgument))->thenReturn(true);

        $mock->fooWithArgument($obj);

        $this->assertSame($obj, $toArgument);
    }

    public function testArgumentCapturingAllValls()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $obj1 = new stdClass;
        $obj2 = new stdClass;
        $obj3 = new stdClass;

        $mock->fooWithArgument($obj1);
        $mock->fooWithArgument($obj2);
        $mock->fooWithArgument($obj3);

        Phake::verify($mock, Phake::atLeast(1))->fooWithArgument(Phake::captureAll($toArgument));

        $this->assertSame(array($obj1, $obj2, $obj3), $toArgument);
    }

    /**
     * Make sure stub return value capturing returns the parent value
     */
    public function testCaptureAnswerReturnsParentValue()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->fooWithReturnValue()->captureReturnTo($return);

        $this->assertEquals('blah', $mock->fooWithReturnValue());
    }

    /**
     * Make sure stub return value capturing returns the parent value
     */
    public function testCaptureAnswerCapturesParentValue()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->fooWithReturnValue()->captureReturnTo($return);

        $mock->fooWithReturnValue();

        $this->assertEquals('blah', $return);
    }

    /**
     * Tests setting reference parameters
     */
    public function testSettingReferenceParameters()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithRefParm('test', Phake::setReference(42))->thenReturn(null);

        $mock->fooWithRefParm('test', $value);

        $this->assertSame(42, $value);
    }

    /**
     * Tests conditional reference parameter setting
     */
    public function testConditionalReferenceParameterSetting()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithRefParm('test', Phake::setReference(42)->when(24))->thenReturn(null);

        $value = 24;
        $mock->fooWithRefParm('test', $value);

        $this->assertSame(42, $value);
    }

    /**
     * Make sure reference parameters aren't set if the conditions don't match
     */
    public function testConditionalReferenceParameterSettingFails()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithRefParm('test', Phake::setReference(42)->when(24))->thenReturn(null);

        $value = 25;
        $mock->fooWithRefParm('test', $value);

        $this->assertSame(25, $value);
    }

    /**
     * Make sure paremeters are set to objects with no issues
     */
    public function testReferenceParameterSettingWorksOnObjects()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $obj = new stdClass;
        Phake::when($mock)->fooWithRefParm('test', Phake::setReference($obj))->thenReturn(null);

        $value = 25;
        $mock->fooWithRefParm('test', $value);

        $this->assertSame($obj, $value);
    }

    /**
     * Tests times matches exactly
     */
    public function testVerifyTimesExact()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->foo();

        Phake::verify($mock, Phake::times(2))->foo();
    }

    /**
     * Tests times doesn't match
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyTimesMismatch()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->foo();

        Phake::verify($mock)->foo();
    }

    /**
     * Tests at least matches with exact calls
     */
    public function testVerifyAtLeastExact()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();

        Phake::verify($mock, Phake::atLeast(1))->foo();
    }

    /**
     * Tests at least matches with greater calls
     */
    public function testVerifyAtLeastGreater()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->foo();

        Phake::verify($mock, Phake::atLeast(1))->foo();
    }

    /**
     * Tests that at least doesn't match
     * @expectedException Phake_Exception_VerificationException
     */
    public function testVerifyAtLeastMismatch()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::verify($mock, Phake::atLeast(1))->foo();
    }

    /**
     * Tests that never matches
     */
    public function testNeverMatches()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::verify($mock, Phake::never())->foo();
    }

    /**
     * Tests that never catches an invocation
     * @expectedException Phake_Exception_VerificationException
     */
    public function testNeverMismatch()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo();
        Phake::verify($mock, Phake::never())->foo();
    }

    /**
     * Tests that atMost passes with exact
     */
    public function testAtMostExactly()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo();
        Phake::verify($mock, Phake::atMost(1))->foo();
    }

    /**
     * Tests that atMost passes with under expected calls
     */
    public function testAtMostUnder()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::verify($mock, Phake::atMost(1))->foo();
    }

    /**
     * Tests that atMost fails on over calls
     * @expectedException Phake_Exception_VerificationException
     */
    public function testAtMostOver()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo();
        $mock->foo();
        Phake::verify($mock, Phake::atMost(1))->foo();
    }

    /**
     * Tests that the given exception is thrown on thenThrow.
     * @expectedException Phake_Exception_VerificationException
     */
    public function testStubThenThrow()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->foo()->thenThrow(new Phake_Exception_VerificationException());
        $mock->foo();
    }

    /**
     * Tests that Phake::anyParameters() returns an instance of Phake_Matchers_AnyParameters
     */
    public function testAnyParameters()
    {
        $matcher = Phake::anyParameters();

        $this->assertInstanceOf("Phake_Matchers_AnyParameters", $matcher);
    }

    /**
     * Tests that Phake::anyParameters() really matches any invocation
     */
    public function testAnyParametersMatchesEverything()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithLotsOfParameters(1, 2, 3);
        $mock->fooWithLotsOfParameters(1, 3, 2);
        $mock->fooWithLotsOfParameters(2, 1, 3);
        $mock->fooWithLotsOfParameters(2, 3, 1);
        $mock->fooWithLotsOfParameters(3, 1, 2);
        $mock->fooWithLotsOfParameters(3, 2, 1);

        Phake::verify($mock, Phake::times(6))->fooWithLotsOfParameters(Phake::anyParameters());
    }

    public function testAnyParametersThrowsAnErrorWithTrailingParameters()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithLotsOfParameters(3, 2, 1);

        $this->setExpectedException('InvalidArgumentException', 'Other matchers cannot be passed with any '
            . 'parameters. It will not work the way you think it works');
        Phake::verify($mock)->fooWithLotsOfParameters(Phake::anyParameters(), 1);
    }

    public function testAnyParametersThrowsAnErrorWithPrecedingParameters()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithLotsOfParameters(3, 2, 1);

        $this->setExpectedException('InvalidArgumentException', 'Other matchers cannot be passed with any '
            . 'parameters. It will not work the way you think it works');
        Phake::verify($mock)->fooWithLotsOfParameters(3, Phake::anyParameters());
    }

    public function testIgnoreRemainingMatchesEverything()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithLotsOfParameters(1, 2, 3);
        $mock->fooWithLotsOfParameters(1, 3, 2);
        $mock->fooWithLotsOfParameters(1, 1, 3);
        $mock->fooWithLotsOfParameters(1, 3, 1);
        $mock->fooWithLotsOfParameters(1, 1, 2);
        $mock->fooWithLotsOfParameters(1, 2, 1);

        Phake::verify($mock, Phake::times(6))->fooWithLotsOfParameters(1, Phake::ignoreRemaining());
    }

    public function testIgnoreRemainingThrowsAnErrorWithTrailingParameters()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->fooWithLotsOfParameters(3, 2, 1);

        $this->setExpectedException('InvalidArgumentException', 'Other matchers cannot be checked after you ignore remaining parameters.');
        Phake::verify($mock)->fooWithLotsOfParameters(Phake::ignoreRemaining(), 1);
    }

    /**
     * Tests that when stubs are defined, they're matched in reverse order.
     */
    public function testMatchesInReverseOrder()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->fooWithArgument($this->anything())->thenReturn(false);
        Phake::when($mock)->fooWithArgument('foo')->thenReturn(true);

        $this->assertTrue($mock->fooWithArgument('foo'));
    }

    public function testFailedVerificationWithNoMockInteractions()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $this->setExpectedException(
            'Phake_Exception_VerificationException',
            'Expected PhakeTest_MockedClass->foo() to be called exactly <1> times, actually called <0> times. In fact, there are no interactions with this mock.'
        );
        Phake::verify($mock)->foo();
    }

    public function testFailedVerificationWithNonmatchingMethodCalls()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo('test');

        $this->setExpectedException(
            'Phake_Exception_VerificationException',
            'Expected PhakeTest_MockedClass->foo() to be called exactly <1> times, actually called <0> times.' . "\n"
                . "Other Invocations:\n"
                . "===\n"
                . "  PhakeTest_MockedClass->foo(<string:test>)\n"
                . "  No matchers were given to Phake::when(), but arguments were received by this method.\n"
                . "==="
        );

        Phake::verify($mock)->foo();
    }

    public function testStubbingMagicCallMethod()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::when($mock)->magicCall()->thenReturn('magicCalled');

        $this->assertEquals('magicCalled', $mock->magicCall());
    }

    public function testVerifyingMagicCallMethod()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        $mock->magicCall();

        Phake::verify($mock)->magicCall();
    }

    public function testStubbingMagicMethodsAlsoResortsToCallIfNoStubsDefined()
    {
        $expected = '__call';
        $mock     = Phake::partialMock('PhakeTest_MagicClass');

        Phake::when($mock)->magicCall()->thenReturn('magicCalled');

        $this->assertEquals('magicCalled', $mock->magicCall());
        $this->assertEquals($expected, $mock->unStubbedCall());
    }

    public function testStubbingMagicStaticCallMethod()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::whenStatic($mock)->magicCall()->thenReturn('magicCalled');

        $this->assertEquals('magicCalled', $mock::magicCall());
    }

    public function testMockingSoapClient()
    {
        // This test requires that E_STRICT be on
        // It will fail with it on, otherwise it wont' complain
        $mock = Phake::mock('SoapClient');

        $this->addToAssertionCount(1);
    }

    public function testDefaultClient()
    {
        $original_client = Phake::getClient();

        Phake::setClient(null);

        $this->assertInstanceOf('Phake_Client_Default', Phake::getClient());

        Phake::setClient($original_client);
    }

    public function testSettingClient()
    {
        $original_client = Phake::getClient();

        $client = Phake::mock('Phake_Client_IClient');
        Phake::setClient($client);

        $this->assertSame($client, Phake::getClient());

        Phake::setClient($original_client);
    }

    public function testSettingDefaultClientByString()
    {
        $original_client = Phake::getClient();

        Phake::setClient(Phake::CLIENT_DEFAULT);

        $this->assertInstanceOf('Phake_Client_Default', Phake::getClient());

        Phake::setClient($original_client);
    }

    public function testSettingPHPUnitClientByString()
    {
        $original_client = Phake::getClient();

        Phake::setClient(Phake::CLIENT_PHPUNIT);

        $this->assertInstanceOf('Phake_Client_PHPUnit', Phake::getClient());

        Phake::setClient($original_client);
    }

    public function testVerifyNoFurtherInteractionPassesStrict()
    {
        Phake::setClient(Phake::CLIENT_PHPUNIT);
        $mock = Phake::mock('stdClass');

        $assertionCount = self::getCount();
        Phake::verifyNoFurtherInteraction($mock);
        $newAssertionCount = self::getCount();

        $this->assertGreaterThan($assertionCount, $newAssertionCount);
    }

    public function testVerifyNoInteractionPassesStrict()
    {
        Phake::setClient(Phake::CLIENT_PHPUNIT);
        $mock = Phake::mock('stdClass');

        $assertionCount = self::getCount();
        Phake::verifyNoInteraction($mock);
        $newAssertionCount = self::getCount();

        $this->assertGreaterThan($assertionCount, $newAssertionCount);
    }

    public function testMockingStaticClass()
    {
        $mock = Phake::mock('PhakeTest_StaticClass');

        Phake::whenStatic($mock)->staticMethod()->thenReturn('bar');

        $this->assertEquals('bar', $mock->staticMethod());
        Phake::verifyStatic($mock)->staticMethod();
    }

    public function testMockingStaticInterface()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        $this->assertInstanceOf('Phake_IMock', $mock);
    }

    public function testCallingMockStaticMethod()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        $this->assertNull($mock::staticMethod());
    }

    public function testVerifyingMockStaticMethod()
    {
        $mock = Phake::mock('PhakeTest_StaticInterface');

        $mock::staticMethod();

        Phake::verifyStatic($mock)->staticMethod();
    }

    public function testMockingAbstractClass()
    {
        $mock = Phake::partialMock('PhakeTest_AbstractClass');
        $this->assertNull($mock->referenceDefault());
    }

    public function testStubbingMemcacheSetMethod()
    {
        if (!extension_loaded('memcache'))
        {
            $this->markTestSkipped('memcache extension not loaded');
        }

        $memcache = Phake::mock('Memcache');

        Phake::when($memcache)->set('key', 'value')->thenReturn(true);

        $this->assertTrue($memcache->set('key', 'value'));
    }

    public function testMockingMethodReturnByReference()
    {
        $something            = array();
        $referenceMethodClass = Phake::mock('PhakeTest_ReturnByReferenceMethodClass');

        Phake::when($referenceMethodClass)->getSomething()->thenReturn($something);

        $something[]     = 'foo';
        $returnSomething = $referenceMethodClass->getSomething();

        $this->assertNotContains('foo', $returnSomething);
    }

    public function testGetOnMockedClass()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');
        Phake::when($mock)->__get('myId')->thenReturn(500)->thenReturn(501);

        $this->assertEquals(500, $mock->myId);
        $this->assertEquals(501, $mock->myId);

        Phake::verify($mock, Phake::times(2))->__get('myId');
    }

    public function testCallOrderInObjectFailsWithPHPUnit()
    {
        Phake::setClient(Phake::CLIENT_PHPUNIT);

        $mock = Phake::mock('PhakeTest_MockedClass');

        $mock->foo();
        $mock->callInnerFunc();
        $mock->fooWithReturnValue();

        $this->setExpectedException('PHPUnit_Framework_ExpectationFailedException');

        Phake::inOrder(
            Phake::verify($mock)->foo(),
            Phake::verify($mock)->fooWithReturnValue(),
            Phake::verify($mock)->callInnerFunc()
        );
    }

    public function testGetMockedClassAnythingMatcher()
    {
        $mock = Phake::mock('PhakeTest_MagicClass');

        Phake::when($mock)->__get($this->anything())->thenReturn(500);

        $this->assertEquals(500, $mock->myId);

        Phake::verify($mock)->__get($this->anything());
    }

    public function testConstructorInterfaceCanBeMocked()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('This test causes a fatal error under HHVM.');
        }

        // Generated a fatal error before fixed
        $this->assertInstanceOf('Phake_IMock', Phake::mock('PhakeTest_ConstructorInterface'));
    }

    public function testClassWithWakeupWorks()
    {
        $this->assertInstanceOf('Phake_IMock', Phake::mock('PhakeTest_WakeupClass'));
    }

    public function testMockPDOStatement()
    {
        $this->assertInstanceOf('PDOStatement', Phake::mock('PDOStatement'));
    }

    public function testMocksNotEqual()
    {
        $chocolateCookie = Phake::mock('PhakeTest_A');
        $berryCookie = Phake::mock('PhakeTest_A');

        $this->assertNotEquals($chocolateCookie, $berryCookie);
    }

    public function testStaticClassesReset()
    {
        $mock1 = Phake::mock('PhakeTest_StaticInterface');
        $mock1::staticMethod();
        Phake::verifyStatic($mock1)->staticMethod();

        Phake::resetStaticInfo();

        $mock2 = Phake::mock('PhakeTest_StaticInterface');
        $mock2::staticMethod();
        Phake::verifyStatic($mock2)->staticMethod();

    }

    public function testMockPDO()
    {
        $this->assertInstanceOf('PDO', Phake::mock('PDO'));
    }

    public function testMockPDOExtendingStatementClass()
    {
        $this->assertInstanceOf(
            'PhakeTest_PDOStatementExtendingClass',
            Phake::mock('PhakeTest_PDOStatementExtendingClass')
        );
    }

    public function testMockPDOExtendingClass()
    {
        $this->assertInstanceOf(
            'PhakeTest_PDOExtendingClass',
            Phake::mock('PhakeTest_PDOExtendingClass')
        );
    }

    public function testMockRedis()
    {
        if (!extension_loaded('redis'))
        {
            $this->markTestSkipped('Cannot run this test without mock redis');
        }

        $mock = Phake::mock('Redis');
        $this->assertInstanceOf('Redis', $mock);
    }

    public function testFinallyBlockFiresVerifications()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $this->markTestSkipped('The finally keyword only exists in php 5.5 and above');
        }


        eval('
            $this->setExpectedException("InvalidArgumentException");
            $mock = Phake::mock("PhakeTest_MockedClass");
            try
            {
                $mock->foo();
                throw new InvalidArgumentException();
            }
            finally
            {
                Phake::verify($mock)->foo();
            }
        ');
    }

    public function testVerifyNoOtherInteractions()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo('a');
        $mock->foo('b');

        Phake::verify($mock)->foo('a');
        $this->setExpectedException('Phake_Exception_VerificationException');
        Phake::verifyNoOtherInteractions($mock);
    }

    public function testVerifyNoOtherInteractionsWorks()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo('a');
        $mock->foo('b');

        Phake::verify($mock)->foo('a');
        Phake::verify($mock)->foo('b');
        Phake::verifyNoOtherInteractions($mock);
    }

    public function testCallingProtectedMethods()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->innerFunc()->thenCallParent();

        $returned = Phake::makeVisible($mock)->innerFunc();

        Phake::verify($mock)->innerFunc();
        $this->assertSame('test', $returned);
    }

    public function testCallingPrivateMethods()
    {
        if (defined('HHVM_VERSION'))
        {
            $this->markTestSkipped("Can't call private methods with hhvm");
        }
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->privateFunc()->thenCallParent();

        $returned = Phake::makeVisible($mock)->privateFunc();

        Phake::verify($mock)->privateFunc();
        $this->assertSame('blah', $returned);
    }

    public function testCallingProtectedStaticMethods()
    {
        $mock = Phake::mock('PhakeTest_StaticClass');
        Phake::whenStatic($mock)->protectedStaticMethod()->thenCallParent();

        $returned = Phake::makeStaticsVisible($mock)->protectedStaticMethod();

        Phake::verifyStatic($mock)->protectedStaticMethod();
        $this->assertSame('foo', $returned);
    }

    public function testThenReturnCallback()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');

        Phake::when($mock)->foo->thenReturnCallback(function () {
            return true;
        });

        $this->assertTrue($mock->foo());
    }

    public function testMockingMultipleInterfaces()
    {
        $mock = Phake::mock(array('PhakeTest_MockedInterface', 'PhakeTest_MockedClass'));
        $this->assertInstanceOf('PhakeTest_MockedInterface', $mock);
        $this->assertInstanceOf('PhakeTest_MockedClass', $mock);

        Phake::when($mock)->foo->thenReturn('bar');
        Phake::when($mock)->reference->thenReturn('foo');
        Phake::when($mock)->fooWithArgument->thenReturn(42);

        $this->assertEquals('bar', $mock->foo());
        $this->assertEquals('foo', $mock->reference($test));
        $this->assertEquals(42, $mock->fooWithArgument('blah'));

        Phake::verify($mock)->foo();
        Phake::verify($mock)->reference(null);
        Phake::verify($mock)->fooWithArgument('blah');
    }

    public function testReturningSelf()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->foo->thenReturnSelf();

        $this->assertSame($mock, $mock->foo());
    }

    public function testResetStaticPostCall() {
        $obj = new PhakeTest_StaticMethod;
        $obj->className = Phake::mock('PhakeTest_ClassWithStaticMethod');
        Phake::whenStatic($obj->className)->ask()->thenReturn('ASKED');

        $val = $obj->askSomething();
        Phake::verifyStatic($obj->className)->ask();

        $this->assertEquals('ASKED', $val);

        $obj->className = Phake::resetStatic($obj->className);

        $val = $obj->askSomething();
        $this->assertEquals('Asked', $val);
    }
}
