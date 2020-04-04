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
 * Tests the function of the StubMapper
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Stubber_StubMapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Stubber_StubMapper
     */
    private $mapper;

    /**
     * Sets up the test fixture
     */
    public function setUp()
    {
        $this->mapper = new Phake_Stubber_StubMapper();
    }

    /**
     * Tests mapping matchers to answers.
     */
    public function testMappingMatchers()
    {
        $matcher = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);
        $stub    = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $matcher->expects($this->any())
            ->method('matches')
            ->with('foo', array('bar', 'test'))
            ->will($this->returnValue(true));

        $matcher->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('foo'));

        $this->mapper->mapStubToMatcher($stub, $matcher);

        $arguments = array('bar', 'test');
        $this->assertEquals($stub, $this->mapper->getStubByCall('foo', $arguments));
    }

    /**
     * Tests mapping matchers to answers.
     */
    public function testMappingMatchersFailsOnNonMatch()
    {
        $matcher = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);
        $stub    = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $matcher->expects($this->any())
            ->method('matches')
            ->will($this->returnValue(false));

        $this->mapper->mapStubToMatcher($stub, $matcher);

        $arguments = array('bar', 'test');
        $this->assertNull($this->mapper->getStubByCall('foo', $arguments));
    }

    /**
     * Tests resetting a stub mapper
     */
    public function testRemoveAllAnswers()
    {
        $matcher = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);
        $stub    = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $matcher->expects($this->never())
            ->method('matches');

        $this->mapper->mapStubToMatcher($stub, $matcher);

        $this->mapper->removeAllAnswers();

        $arguments = array('bar', 'test');
        $this->assertNull($this->mapper->getStubByCall('foo', $arguments));
    }

    /**
     * Tests matches in reverse order.
     */
    public function testMatchesInReverseOrder()
    {
        $match_me      = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);
        $match_me_stub = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $also_matches      = $this->getMock('Phake_Matchers_MethodMatcher', array(), array(), '', false);
        $also_matches_stub = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $also_matches->expects($this->never())
            ->method('matches');

        $also_matches->expects($this->any())
            ->method('getMethod')
            ->will($this->returnvalue('foo'));

        $match_me->expects($this->any())
            ->method('matches')
            ->with('foo', array('bar', 'test'))
            ->will($this->returnValue(true));

        $match_me->expects($this->any())
            ->method('getMethod')
            ->will($this->returnvalue('foo'));

        $this->mapper->mapStubToMatcher($also_matches_stub, $also_matches);
        $this->mapper->mapStubToMatcher($match_me_stub, $match_me);

        $arguments = array('bar', 'test');
        $this->assertEquals($match_me_stub, $this->mapper->getStubByCall('foo', $arguments));
    }

    public function testMappingParameterSetter()
    {
        $matcher = new Phake_Matchers_MethodMatcher('method', new Phake_Matchers_ReferenceSetter(42));
        $stub    = $this->getMock('Phake_Stubber_AnswerCollection', array(), array(), '', false);

        $value        = 'blah';
        $arguments    = array();
        $arguments[0] =& $value;

        $this->mapper->mapStubToMatcher($stub, $matcher);

        $this->assertEquals($stub, $this->mapper->getStubByCall('method', $arguments));

        $this->assertEquals(42, $value);
    }
}


