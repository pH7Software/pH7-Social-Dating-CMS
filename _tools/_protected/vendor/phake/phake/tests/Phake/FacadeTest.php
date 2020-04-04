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
 * Tests the facade class for Phake
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_FacadeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Facade
     */
    private $facade;

    /**
     * @var Phake_ClassGenerator_MockClass
     */
    private $mockGenerator;

    /**
     * @Mock
     * @var Phake_Mock_InfoRegistry
     */
    private $infoRegistry;

    /**
     * Sets up the mock generator
     */
    public function setup()
    {
        Phake::initAnnotations($this);
        $this->mockGenerator = $this->getMock('Phake_ClassGenerator_MockClass');
        $this->facade        = new Phake_Facade($this->infoRegistry);
    }

    /**
     * Tests that the mock generator is called properly
     */
    public function testMock()
    {
        $mockedClass   = 'stdClass';
        $mockGenerator = $this->getMock('Phake_ClassGenerator_MockClass');

        $this->setMockGeneratorExpectations($mockedClass, $mockGenerator);

        $this->facade->mock(
            $mockedClass,
            $mockGenerator,
            $this->getMock('Phake_CallRecorder_Recorder'),
            $this->getMock('Phake_Stubber_IAnswer')
        );
    }

    /**
     * Tests that the mock generator is called properly
     */
    public function testMockInterface()
    {
        $mockedClass   = 'PhakeTest_MockedInterface';
        $mockGenerator = $this->getMock('Phake_ClassGenerator_MockClass');

        $this->setMockGeneratorExpectations($mockedClass, $mockGenerator);

        $this->facade->mock(
            $mockedClass,
            $mockGenerator,
            $this->getMock('Phake_CallRecorder_Recorder'),
            $this->getMock('Phake_Stubber_IAnswer')
        );
    }

    /**
     * Tests that the mock generator will fail when given a class that does not exist.
     * @expectedException InvalidArgumentException
     */
    public function testMockThrowsOnNonExistantClass()
    {
        $mockedClass = 'NonExistantClass';

        $this->facade->mock(
            $mockedClass,
            $this->getMock('Phake_ClassGenerator_MockClass'),
            $this->getMock('Phake_CallRecorder_Recorder'),
            $this->getMock('Phake_Stubber_IAnswer')
        );
    }

    /**
     * Tests that Phake will pass necessary components to a generated class when instantiating it.
     */
    public function testMockPassesNecessaryComponentsToInstantiatedClass()
    {
        $mockedClass = 'stdClass';

        $recorder       = $this->getMock('Phake_CallRecorder_Recorder');
        $classGenerator = $this->getMock('Phake_ClassGenerator_MockClass');
        $answer         = $this->getMock('Phake_Stubber_IAnswer');


        $this->setMockInstantiatorExpectations($classGenerator, $recorder, $answer);

        $this->facade->mock($mockedClass, $classGenerator, $recorder, $answer);
    }

    /**
     * Test that autoload doesn't get called on generated classes
     */
    public function testAutoLoadNotCalledOnMock()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
        try {
            $mockedClass   = 'stdClass';
            $mockGenerator = $this->getMock('Phake_ClassGenerator_MockClass');

            //This test will fail if the autoload below is called
            $this->facade->mock(
                $mockedClass,
                $mockGenerator,
                $this->getMock('Phake_CallRecorder_Recorder'),
                $this->getMock('Phake_Stubber_IAnswer')
            );
            spl_autoload_unregister(array(__CLASS__, 'autoload'));
        }
        catch (Exception $e)
        {
            spl_autoload_unregister(array(__CLASS__, 'autoload'));
            throw $e;
        }
    }

    /**
     * An autoload function that should never be called
     */
    public static function autoload()
    {
        $e = new Exception;
        self::fail("The autoloader should not be called: \n{$e->getTraceAsString()}");
    }

    public function testReset()
    {
        $this->facade->resetStaticInfo();

        Phake::verify($this->infoRegistry)->resetAll();
    }

    /**
     * Sets expectations for how the generator should be called
     *
     * @param string                         $mockedClass - The class name that we expect to mock
     * @param Phake_ClassGenerator_MockClass $mockGenerator
     */
    private function setMockGeneratorExpectations($mockedClass, Phake_ClassGenerator_MockClass $mockGenerator)
    {
        $mockGenerator->expects($this->once())
            ->method('generate')
            ->with($this->matchesRegularExpression('#^[A-Za-z0-9_]+$#'), $this->equalTo((array)$mockedClass), $this->equalTo($this->infoRegistry));
    }

    /**
     * Sets expectations for how the mock class should be created from the class generator
     *
     * @param Phake_ClassGenerator_MockClass $mockGenerator
     * @param Phake_CallRecorder_Recorder    $recorder
     * @param Phake_Stubber_IAnswer          $answer
     */
    private function setMockInstantiatorExpectations(
        Phake_ClassGenerator_MockClass $mockGenerator,
        Phake_CallRecorder_Recorder $recorder,
        Phake_Stubber_IAnswer $answer
    ) {
        $mockGenerator->expects($this->once())
            ->method('instantiate')
            ->with(
                $this->matchesRegularExpression('#^[A-Za-z0-9_]+$#'),
                $this->equalTo($recorder),
                $this->isInstanceOf('Phake_Stubber_StubMapper'),
                $this->equalTo($answer)
            );
    }
}


