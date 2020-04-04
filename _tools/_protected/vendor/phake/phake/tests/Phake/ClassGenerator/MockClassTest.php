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
 * Description of MockClass
 */
class Phake_ClassGenerator_MockClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_ClassGenerator_MockClass
     */
    private $classGen;

    /**
     * @Mock
     * @var Phake_Mock_InfoRegistry
     */
    private $infoRegistry;

    public function setUp()
    {
        Phake::initAnnotations($this);
        $this->classGen = new Phake_ClassGenerator_MockClass();
    }

    /**
     * Tests the generate method of the mock class generator.
     */
    public function testGenerateCreatesClass()
    {
        $newClassName = __CLASS__ . '_TestClass1';
        $mockedClass  = 'stdClass';

        $this->assertFalse(
            class_exists($newClassName, false),
            'The class being tested for already exists. May have created a test reusing this class name.'
        );

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $this->assertTrue(
            class_exists($newClassName, false),
            'Phake_ClassGenerator_MockClass::generate() did not create correct class'
        );
    }

    /**
     * Tests that the generate method will create a class that extends a given class.
     */
    public function testGenerateCreatesClassExtendingExistingClass()
    {
        $newClassName = __CLASS__ . '_TestClass2';
        $mockedClass  = 'stdClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $rflClass = new ReflectionClass($newClassName);

        $this->assertTrue(
            $rflClass->isSubclassOf($mockedClass),
            'Phake_ClassGenerator_MockClass::generate() did not create a class that extends mocked class.'
        );
    }

    /**
     * Tests that generated mock classes will accept and provide access too a call recorder.
     */
    public function testGenerateCreatesClassWithExposedCallRecorder()
    {
        $newClassName = __CLASS__ . '_TestClass3';
        $mockedClass  = 'stdClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = $this->getMock('Phake_Stubber_IAnswer');
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $this->assertSame($callRecorder, Phake::getInfo($mock)->getCallRecorder());
    }

    /**
     * Tests that generated mock classes will record calls to mocked methods.
     */
    public function testCallingMockedMethodRecordsCall()
    {
        $newClassName = __CLASS__ . '_TestClass4';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = new Phake_Stubber_Answers_NoAnswer();
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        /* @var $callRecorder Phake_CallRecorder_Recorder */
        $callRecorder->expects($this->once())
            ->method('recordCall')
            ->with($this->equalTo(new Phake_CallRecorder_Call($mock, 'foo', array())));

        $mock->foo();
    }

    /**
     * Tests that calls are recorded with arguments
     */
    public function testCallingmockedMethodRecordsArguments()
    {
        $newClassName = __CLASS__ . '_TestClass9';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = new Phake_Stubber_Answers_NoAnswer();
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        /* @var $callRecorder Phake_CallRecorder_Recorder */
        $callRecorder->expects($this->once())
            ->method('recordCall')
            ->with(
                $this->equalTo(
                    new Phake_CallRecorder_Call($mock, 'fooWithArgument', array('bar'))
                )
            );

        $mock->fooWithArgument('bar');
    }

    public function testGeneratingClassFromMultipleInterfaces()
    {
        $newClassName = __CLASS__ . '_testClass28';
        $mockedClass = array('PhakeTest_MockedInterface', 'PhakeTest_ConstructorInterface');

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $reflClass = new ReflectionClass($newClassName);
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedInterface'), "Implements PhakeTest_MockedInterface");
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_ConstructorInterface'), "Implements PhakeTest_ConstructorInterface");
    }

    public function testGeneratingClassFromSimilarInterfaces()
    {
        $newClassName = __CLASS__ . '_testClass29';
        $mockedClass = array('PhakeTest_MockedInterface', 'PhakeTest_MockedInterface2');

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $reflClass = new ReflectionClass($newClassName);
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedInterface'), "Implements PhakeTest_MockedInterface");
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedInterface2'), "Implements PhakeTest_ConstructorInterface");
    }

    public function testGeneratingClassFromDuplicateInterfaces()
    {
        $newClassName = __CLASS__ . '_testClass30';
        $mockedClass = array('PhakeTest_MockedInterface', 'PhakeTest_MockedInterface');

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $reflClass = new ReflectionClass($newClassName);
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedInterface'), "Implements PhakeTest_MockedInterface");
    }

    public function testGeneratingClassFromInheritedInterfaces()
    {
        $newClassName = __CLASS__ . '_testClass31';
        $mockedClass = array('PhakeTest_MockedInterface', 'PhakeTest_MockedChildInterface');

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $reflClass = new ReflectionClass($newClassName);
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedInterface'), "Implements PhakeTest_MockedInterface");
        $this->assertTrue($reflClass->implementsInterface('PhakeTest_MockedChildInterface'), "Implements PhakeTest_MockedInterface");
    }

    public function testGeneratingClassFromMultipleClasses()
    {
        $newClassName = __CLASS__ . '_testClass32';
        $mockedClass = array('PhakeTest_MockedClass', 'PhakeTest_MockedConstructedClass');

        $this->setExpectedException('RuntimeException');
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);
    }

    /**
     * Tests the instantiation functionality of the mock generator.
     */
    public function testInstantiate()
    {
        $newClassName = __CLASS__ . '_TestClass5';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $this->assertInstanceOf($newClassName, $mock);
    }

    /**
     * Tests that calling a stubbed method will result in the stubbed answer being returned.
     * @group testonly
     */
    public function testStubbedMethodsReturnStubbedAnswer()
    {
        $newClassName = __CLASS__ . '_TestClass7';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());

        $stubMapper->expects($this->once())
            ->method('getStubByCall')
            ->with($this->equalTo('fooWithArgument'), array('bar'))
            ->will($this->returnValue(new Phake_Stubber_AnswerCollection($answer)));

        $mock->fooWithArgument('bar');

        Phake::verify($answer)->getAnswerCallback($mock, 'fooWithArgument');
    }

    /**
     * Tests that default parameters work correctly with stubbing
     */
    public function testStubbedMethodDoesNotCheckUnpassedDefaultParameters()
    {
        $newClassName = __CLASS__ . '_TestClass23';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $stubMapper->expects($this->once())
            ->method('getStubByCall')
            ->with($this->equalTo('fooWithDefault'), array())
            ->will($this->returnValue(new Phake_Stubber_AnswerCollection($answer)));

        $mock->fooWithDefault();

        Phake::verify($answer)->getAnswerCallback($mock, 'fooWithDefault');
    }

    /**
     * Tests that generated mock classes will allow setting stubs to methods. This is delegated
     * internally to the stubMapper
     */
    public function testStubbableInterface()
    {
        $newClassName = __CLASS__ . '_TestClass8';
        $mockedClass  = 'stdClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $callRecorder Phake_CallRecorder_Recorder */
        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        /** @var $stubMapper Phake_Stubber_StubMapper */
        $stubMapper = $this->getMock('Phake_Stubber_StubMapper');
        $answer     = $this->getMock('Phake_Stubber_IAnswer');
        $mock       = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $answer           = $this->getMock('Phake_Stubber_IAnswer');
        $answerCollection = new Phake_Stubber_AnswerCollection($answer);
        $matcher          = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);

        $stubMapper->expects($this->once())
            ->method('mapStubToMatcher')
            ->with($this->equalTo($answerCollection), $this->equalTo($matcher));

        Phake::getInfo($mock)->getStubMapper()->mapStubToMatcher($answerCollection, $matcher);
    }

    /**
     * Tests that calling an unstubbed method will result in the default answer being returned.
     */
    public function testUnstubbedMethodsReturnDefaultAnswer()
    {
        $newClassName = __CLASS__ . '_TestClass11';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());

        $mock = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $mock->fooWithArgument('bar');

        Phake::verify($answer)->getAnswerCallback($mock, 'fooWithArgument');
    }

    /**
     * Tests that __call on an unmatched method will return a default value
     */
    public function testUnstubbedCallReturnsDefaultAnswer()
    {
        $newClassName = __CLASS__ . '_TestClass19';
        $mockedClass  = 'PhakeTest_MagicClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());

        $mock = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $mock->fooWithArgument('bar');

        Phake::verify($answer)->getAnswerCallback($mock, '__call');
    }

    public function testMagicCallMethodsRecordTwice()
    {
        $newClassName = __CLASS__ . '_TestClass21';
        $mockedClass  = 'PhakeTest_MagicClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = Phake::mock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $mock->foo('blah');

        Phake::verify($callRecorder)->recordCall(
            new Phake_CallRecorder_Call($mock, 'foo', array('blah'))
        );
        Phake::verify($callRecorder)->recordCall(
            new Phake_CallRecorder_Call($mock, '__call', array('foo', array('blah')))
        );
    }

    public function testMagicCallChecksFallbackStub()
    {
        $newClassName = __CLASS__ . '_TestClass22';
        $mockedClass  = 'PhakeTest_MagicClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = Phake::mock('Phake_CallRecorder_Recorder');
        $stubMapper   = Phake::mock('Phake_Stubber_StubMapper');
        $answer       = Phake::mock('Phake_Stubber_Answers_NoAnswer', Phake::ifUnstubbed()->thenCallParent());
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);


        $mock->foo('blah');

        Phake::verify($stubMapper)->getStubByCall('foo', array('blah'));
        Phake::verify($stubMapper)->getStubByCall('__call', array('foo', array('blah')));
    }

    /**
     * Tests generating a class definition for a mocked interface
     */
    public function testGenerateOnInterface()
    {
        $newClassName = __CLASS__ . '_TestClass13';
        $mockedClass  = 'PhakeTest_MockedInterface';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $this->assertTrue(
            class_exists($newClassName, false),
            'Phake_ClassGenerator_MockClass::generate() did not create correct class'
        );
    }

    /**
     * Test retrieving mock name
     */
    public function testMockName()
    {
        $newClassName = __CLASS__ . '_TestClass18';
        $mockedClass  = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        $stubMapper   = $this->getMock('Phake_Stubber_StubMapper');
        $answer       = $this->getMock('Phake_Stubber_IAnswer');
        $mock         = $this->classGen->instantiate($newClassName, $callRecorder, $stubMapper, $answer);

        $this->assertEquals('PhakeTest_MockedClass', $mock::__PHAKE_name);
        $this->assertEquals('PhakeTest_MockedClass', Phake::getInfo($mock)->getName());
    }

    /**
     * Tests that passing constructor arguments to the derived class will cause the original constructor to be called.
     */
    public function testCallingOriginalConstructor()
    {
        $newClassName = __CLASS__ . '_TestClass16';
        $mockedClass  = 'PhakeTest_MockedConstructedClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $callRecorder Phake_CallRecorder_Recorder */
        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        /** @var $stubMapper Phake_Stubber_StubMapper */
        $stubMapper = $this->getMock('Phake_Stubber_StubMapper');
        $answer     = new Phake_Stubber_Answers_ParentDelegate();
        $mock       = $this->classGen->instantiate(
            $newClassName,
            $callRecorder,
            $stubMapper,
            $answer,
            array('val1', 'val2', 'val3')
        );

        $this->assertEquals('val1', $mock->getProp1());
        $this->assertEquals('val2', $mock->getProp2());
        $this->assertEquals('val3', $mock->getProp3());
    }

    /**
     * Tests that passing constructor arguments to the derived class will cause the original constructor to be called.
     */
    public function testCallingFinalOriginalConstructor()
    {
        $newClassName = __CLASS__ . '_TestClass26';
        $mockedClass  = 'PhakeTest_MockedFinalConstructedClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $callRecorder Phake_CallRecorder_Recorder */
        $callRecorder = $this->getMock('Phake_CallRecorder_Recorder');
        /** @var $stubMapper Phake_Stubber_StubMapper */
        $stubMapper = $this->getMock('Phake_Stubber_StubMapper');
        $answer     = new Phake_Stubber_Answers_ParentDelegate();
        $mock       = $this->classGen->instantiate(
            $newClassName,
            $callRecorder,
            $stubMapper,
            $answer,
            array('val1', 'val2', 'val3')
        );

        $this->assertEquals('val1', $mock->getProp1());
        $this->assertEquals('val2', $mock->getProp2());
        $this->assertEquals('val3', $mock->getProp3());
    }


    /**
     * Tests the generate method of the mock class generator.
     */
    public function testGenerateCreatesClassWithConstructorInInterfaceButNotInAbstractClass()
    {
        $newClassName = __CLASS__ . '_TestClass27';
        $mockedClass = 'PhakeTest_ImplementConstructorInterface';

        $this->assertFalse(
            class_exists($newClassName, false),
            'The class being tested for already exists. May have created a test reusing this class name.'
        );

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $this->assertTrue(
            class_exists($newClassName, false),
            'Phake_ClassGenerator_MockClass::generate() did not create correct class'
        );
    }

    /**
     * Tests that final methods are not overridden
     */
    public function testFinalMethodsAreNotOverridden()
    {
        $newClassName = __CLASS__ . '_TestClass17';
        $mockedClass  = 'PhakeTest_FinalMethod';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests that the mocked object's __toString() method returns a string by default.
     */
    public function testToStringReturnsString()
    {
        $newClassName = __CLASS__ . '_TestClass24';
        $mockedClass  = 'PhakeTest_ToStringMethod';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $recorder Phake_CallRecorder_Recorder */
        $recorder = $this->getMock('Phake_CallRecorder_Recorder');
        $mapper   = new Phake_Stubber_StubMapper();
        $answer   = new Phake_Stubber_Answers_ParentDelegate();

        $mock = $this->classGen->instantiate($newClassName, $recorder, $mapper, $answer);

        $string = $mock->__toString();

        $this->assertNotNull($string, '__toString() should not return NULL');
        $this->assertEquals('Mock for PhakeTest_ToStringMethod', $string);
    }

    public function testDestructMocked()
    {
        $newClassName = __CLASS__ . '_TestClass' . uniqid();
        $mockedClass  = 'PhakeTest_DestructorClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $recorder Phake_CallRecorder_Recorder */
        $recorder = $this->getMock('Phake_CallRecorder_Recorder');
        $mapper   = new Phake_Stubber_StubMapper();
        $answer   = new Phake_Stubber_Answers_ParentDelegate();

        $mock = $this->classGen->instantiate($newClassName, $recorder, $mapper, $answer);

        unset($mock);
    }

    public function testSerializableMock()
    {
        $newClassName = __CLASS__ . '_TestClass' . uniqid();
        $mockedClass  = 'PhakeTest_SerializableClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /** @var $recorder Phake_CallRecorder_Recorder */
        $recorder = $this->getMock('Phake_CallRecorder_Recorder');
        $mapper   = new Phake_Stubber_StubMapper();
        $answer   = new Phake_Stubber_Answers_ParentDelegate();

        try {
            $mock = $this->classGen->instantiate($newClassName, $recorder, $mapper, $answer);
            $this->assertInstanceOf('PhakeTest_SerializableClass', $mock);
        } catch(\Exception $e) {
            $this->fail("Can't instantiate Serializable Object");
        }
    }

    public function testMocksTraversable()
    {
        $this->assertInstanceOf('Traversable', Phake::mock('Traversable'));
    }

    public function testTraversableExtendedInterfaceIncludesOriginalInterface()
    {
        $this->assertInstanceOf('PhakeTest_TraversableInterface', Phake::mock('PhakeTest_TraversableInterface'));
    }

    /**
     * Ensure that 'callable' type hints in method parameters are supported.
     */
    public function testCallableTypeHint ()
    {
        if (!version_compare(PHP_VERSION, '5.4', '>='))
        {
            $this->markTestSkipped('callable typehint require PHP 5.4');
        }

        $this->assertInstanceOf('PhakeTest_CallableTypehint', Phake::mock('PhakeTest_CallableTypehint'));
    }

    public function testMockVariableNumberOfArguments()
    {
        $mockedClass = Phake::mock('PhakeTest_MockedClass');
        list($arg1, $arg2, $arg3) = array(1, 2, 3);
        $mockedClass->fooWithVariableNumberOfArguments($arg1, $arg2, $arg3);

        Phake::verify($mockedClass)->fooWithVariableNumberOfArguments(1, 2, 3);
    }

    public function testGeneratedMockClassHasStaticInfo()
    {
        $newClassName = __CLASS__ . '_TestClass' . uniqid();
        $mockedClass  = 'stdClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        /* @var $info Phake_Mock_Info */
        $info = $newClassName::$__PHAKE_staticInfo;
        $this->assertInstanceOf('Phake_Mock_Info', $info);

        $this->assertInstanceOf('Phake_Stubber_IAnswer', $info->getDefaultAnswer());
        $this->assertEquals($mockedClass, $info->getName());
        $this->assertInstanceOf('Phake_CallRecorder_Recorder', $info->getCallRecorder());
        $this->assertInstanceOf('Phake_Stubber_StubMapper', $info->getStubMapper());
        $this->assertInstanceOf('Phake_ClassGenerator_InvocationHandler_IInvocationHandler', $info->getHandlerChain());
    }

    public function testGeneratedMockAddsSelfToRegistry()
    {
        $newClassName = __CLASS__ . '_TestClass' . uniqid();
        $mockedClass  = 'stdClass';
        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        Phake::verify($this->infoRegistry)->addInfo($newClassName::$__PHAKE_staticInfo);
	}

    /**
     * Test that the generated mock has the same doc mocked class
     */
    public function testGenerateMaintainsPhpDoc()
    {
        $newClassName = __CLASS__ . '_TestClass25';
        $mockedClass = 'PhakeTest_MockedClass';

        $this->classGen->generate($newClassName, $mockedClass, $this->infoRegistry);

        $rflClass = new ReflectionClass($newClassName);

        $this->assertFalse($rflClass->getMethod("foo")->getDocComment());
        $this->assertEquals(
            "/**\n     * @return void\n     */",
            $rflClass->getMethod("fooWithComment")->getDocComment()
        );
    }

    public function testStubbingVariadics()
    {
        if (version_compare(phpversion(), '5.6.0') < 0)
        {
            $this->markTestSkipped('Variadics are not supported in PHP versions prior to 5.6');
        }

        $mock = Phake::mock('PhakeTest_Variadic');

        Phake::when($mock)->variadicMethod->thenCallParent();

        $this->assertEquals(array(1,2,3,4), $mock->variadicMethod(1, 2, 3, 4));
    }

    public function testMockingVariadics()
    {
        if (version_compare(phpversion(), '5.6.0') < 0)
        {
            $this->markTestSkipped('Variadics are not supported in PHP versions prior to 5.6');
        }

        $mock = Phake::mock('PhakeTest_Variadic');

        $mock->variadicMethod(1, 2, 3, 4, 5, 6);

        Phake::verify($mock)->variadicMethod(1, 2, 3, 4, 5, 6);
    }

    public function testStubbingScalarReturnHints()
    {
        if (version_compare(phpversion(), '7.0.0RC1') < 0)
        {
            $this->markTestSkipped('Scalar type hints are not supported in PHP versions prior to 7.0');
        }

        $mock = Phake::mock('PhakeTest_ScalarTypes');

        Phake::when($mock)->scalarHints->thenReturn(2);

        $this->assertEquals(2, $mock->scalarHints(1, 1));
    }

    public function testStubbingScalarReturnsWrongType()
    {
        if (version_compare(phpversion(), '7.0.0RC1') < 0)
        {
            $this->markTestSkipped('Scalar type hints are not supported in PHP versions prior to 7.0');
        }

        $mock = Phake::mock('PhakeTest_ScalarTypes');

        Phake::when($mock)->scalarHints->thenReturn(array());

        try
        {
            $this->assertEquals(array(), $mock->scalarHints(1, 1));
        }
        catch (TypeError $e)
        {
            return;
        }
        catch (Throwable $e)
        {
            $this->fail("Expected A Type Error, instead got " . get_class($e) . " {$e}");
        }
        $this->fail("Expected A Type Error, no error received");
    }

    public function testDefaultStubChanged()
    {
        if (version_compare(phpversion(), '7.0.0RC1') < 0)
        {
            $this->markTestSkipped('Scalar type hints are not supported in PHP versions prior to 7.0');
        }

        $mock = Phake::mock('PhakeTest_ScalarTypes');

        $mock->scalarHints(1, 1);

        Phake::verify($mock)->scalarHints(1, 1);
    }

    public function testVoidStubReturnsProperly()
    {
        if (version_compare(phpversion(), '7.1.0') < 0)
        {
            $this->markTestSkipped('Void type hints are not supported in PHP versions prior to 7.1');
        }

        $mock = Phake::mock('PhakeTest_VoidType');

        $this->assertNull($mock->voidMethod());

        Phake::verify($mock)->voidMethod();
    }

    public function testVoidStubThrowsException()
    {
        if (version_compare(phpversion(), '7.1.0') < 0)
        {
            $this->markTestSkipped('Void type hints are not supported in PHP versions prior to 7.1');
        }

        $mock = Phake::mock('PhakeTest_VoidType');

        $expectedException = new Exception("Test Exception");
        Phake::when($mock)->voidMethod->thenThrow($expectedException);

        try
        {
            $mock->voidMethod();
            $this->fail("The mocked void method did not throw an exception");
        }
        catch (Exception $actualException)
        {
            $this->assertSame($expectedException, $actualException, "The same exception was not thrown");
        }
    }


    public function testVoidStubCanCallParent()
    {
        if (version_compare(phpversion(), '7.1.0') < 0)
        {
            $this->markTestSkipped('Void type hints are not supported in PHP versions prior to 7.1');
        }

        $mock = Phake::mock('PhakeTest_VoidType');

        Phake::when($mock)->voidMethod->thenCallParent();

        $mock->voidMethod();

        $this->assertEquals(1, $mock->voidCallCount, "Void call count was not incremented, looks like callParent doesn't work");
    }
}

