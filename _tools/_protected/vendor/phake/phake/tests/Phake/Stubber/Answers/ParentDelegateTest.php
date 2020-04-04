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
 * Tests the functionality of the parent delegate
 */
class Phake_Stubber_Answers_ParentDelegateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Stubber_Answers_ParentDelegate
     */
    private $delegate;

    /**
     * Sets up the test fixture
     */
    public function setUp()
    {
        $this->delegate = new Phake_Stubber_Answers_ParentDelegate();
    }

    /**
     * Tets that the delegate returns a callback to the parent class.
     */
    public function testThatDelegateReturnsCorrectCallback()
    {
        $m = Phake::mock('PhakeTest_MockedClass');
        $callback = $this->delegate->getAnswerCallback($m, 'fooWithReturnValue');

        if (defined('HHVM_VERSION'))
        {
            $this->assertEquals(array('parent', 'fooWithReturnValue'), $callback);
        }
        else
        {
            $this->assertEquals('blah', $callback(array()));
        }
    }

    /**
     * Tests that processAnswer will set the captured value
     */
    public function testProcessAnswerSetsCapturedValue()
    {
        $value    = null;
        $delegate = new Phake_Stubber_Answers_ParentDelegate($value);
        $delegate->processAnswer("test");

        $this->assertEquals("test", $value);
    }

    public function testFallbackReturnNull()
    {
        $this->assertNull($this->delegate->getFallback());
    }

    public function testGetAnswerCallbackReturnsFallbackOnMethodsWithNoParents()
    {
        $abstractMock = Phake::mock('PhakeTest_AbstractClass');
        $callback = $this->delegate->getAnswerCallback($abstractMock, 'bar');

        $this->assertEquals(array($this->delegate, 'getFallback'), $callback);
    }

    public function testGetCallbackReturnsFallbackOnClassesWithNoParents()
    {
        $callback = $this->delegate->getAnswerCallback('PhakeTest_MockedClass', 'foo');

        $this->assertEquals(array($this->delegate, 'getFallback'), $callback);
    }

    public function testGetCallbackReturnsFallbackOnClassesWithNoMethod()
    {
        $callback = $this->delegate->getAnswerCallback('PhakeTest_ExtendedMockedConstructedClass', 'methodThatDoesntExist');

        $this->assertEquals(array($this->delegate, 'getFallback'), $callback);
    }

    public function testGetCallbackReturnsFallbackClassThatDoesntExist()
    {
        $callback = $this->delegate->getAnswerCallback('ClassThatDoesntExist', 'methodThatDoesntExist');

        $this->assertEquals(array($this->delegate, 'getFallback'), $callback);
    }

    public function testCallBackCanCallPrivateInTheParent()
    {
        if (defined('HHVM_VERSION'))
        {
            $this->markTestSkipped("Can't call private methods with hhvm");
        }

        $callback = $this->delegate->getAnswerCallback(Phake::mock('PhakeTest_MockedClass'), 'privateFunc');

        $this->assertEquals('blah', call_user_func($callback, array()));
    }

    public function testCallBackCanCallPrivateStaticInTheParent()
    {
        if (defined('HHVM_VERSION'))
        {
            $this->markTestSkipped("Can't call private methods with hhvm");
        }

        $callback = $this->delegate->getAnswerCallback(Phake::mock('PhakeTest_MockedClass'), 'privateStaticFunc');

        $this->assertEquals('blah static', call_user_func($callback, array()));
    }
}


