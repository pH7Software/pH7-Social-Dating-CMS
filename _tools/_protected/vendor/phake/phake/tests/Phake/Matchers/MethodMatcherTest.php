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

class Phake_Matchers_MethodMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Matchers_MethodMatcher
     */
    private $matcher;

    /**
     * @Mock
     * @var Phake_Matchers_IChainableArgumentMatcher
     */
    private $rootArgumentMatcher;

    /**
     * @var array
     */
    private $arguments;

    public function setUp()
    {
        Phake::initAnnotations($this);

        $this->matcher   = new Phake_Matchers_MethodMatcher('foo', $this->rootArgumentMatcher);
    }

    /**
     * Tests that the method matcher will forward arguments on.
     */
    public function testMatchesForwardsParameters()
    {
        $arguments = array('foo', 'bar');
        $this->matcher->matches('foo', $arguments);

        Phake::verify($this->rootArgumentMatcher)->doArgumentsMatch(array('foo', 'bar'));
    }

    /**
     * Tests that the method matcher will return true when all is well.
     */
    public function testMatchesSuccessfullyMatches()
    {
        Phake::when($this->rootArgumentMatcher)->doArgumentsMatch->thenReturn(true);

        $arguments = array('foo', 'bar');
        $this->assertTrue($this->matcher->matches('foo', $arguments));
    }

    /**
     * Tests that the matcher will return false on mismatched method name.
     */
    public function testNoMatcherOnBadMethod()
    {
        Phake::when($this->rootArgumentMatcher)->doArgumentsMatch->thenReturn(true);

        $arguments = array('foo', 'bar');
        $this->assertFalse($this->matcher->matches('test', $arguments));
    }

    /**
     * Tests that the matcher will return false on mismatched argument 1.
     */
    public function testNoMatcherOnBadArg1()
    {
        Phake::when($this->rootArgumentMatcher)->doArgumentsMatch->thenThrow(new Phake_Exception_MethodMatcherException);

        $arguments = array('foo', 'bar');
        $this->assertFalse($this->matcher->matches('foo', $arguments));
    }

    public function testAnyParameterMatching()
    {
        $matcher = new Phake_Matchers_MethodMatcher('method', new Phake_Matchers_AnyParameters());

        $arguments = array(1, 2, 3);
        $this->assertTrue($matcher->matches('method', $arguments));
        $arguments = array(2, 3, 4);
        $this->assertTrue($matcher->matches('method', $arguments));
        $arguments = array(3, 4, 5);
        $this->assertTrue($matcher->matches('method', $arguments));
    }

    public function testSetterMatcher()
    {
        $matcher = new Phake_Matchers_MethodMatcher('method', new Phake_Matchers_ReferenceSetter(42));

        $value        = 'blah';
        $arguments    = array();
        $arguments[0] =& $value;

        $matcher->matches('method', $arguments);

        $this->assertEquals(42, $value);
    }

    public function testNullMatcherWithNoArguments()
    {
        $matcher = new Phake_Matchers_MethodMatcher('method', null);

        $emptyArray = array();
        $this->assertTrue($matcher->matches('method', $emptyArray));
    }

    public function testNullMatcherWithArguments()
    {
        $matcher = new Phake_Matchers_MethodMatcher('method', null);

        $arguments = array('foo');
        $this->assertFalse($matcher->matches('method', $arguments));
    }
}


