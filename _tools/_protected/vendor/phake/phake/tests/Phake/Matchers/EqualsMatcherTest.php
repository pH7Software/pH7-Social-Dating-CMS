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
 * Tests the functionality of the equals matcher
 */
class Phake_Matchers_EqualsMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Matchers_EqualsMatcher
     */
    private $matcher;

    /**
     * Sets up the test fixture
     */
    public function setUp()
    {
        $this->matcher = new Phake_Matchers_EqualsMatcher('foo', new \SebastianBergmann\Comparator\Factory());
    }

    /**
     * Tests that matches return true
     */
    public function testMatches()
    {
        $value = array('foo');
        $this->assertNull($this->matcher->doArgumentsMatch($value));
    }

    /**
     * Tests that non-matches return false
     */
    public function testBadMatches()
    {
        $value = array('test');
        $this->setExpectedException('Exception');
        $this->matcher->doArgumentsMatch($value);
    }

    public function testToString()
    {
        $this->assertEquals('equal to <string:foo>', $this->matcher->__toString());
    }

    /**
     * Tests that the equals matcher __toString function will work on values that don't implement __toString.
     *
     * Closes Issue #14
     */
    public function testToStringOnNonStringableObject()
    {
        $this->matcher = new Phake_Matchers_EqualsMatcher(new stdClass, new \SebastianBergmann\Comparator\Factory());

        $this->assertEquals('equal to <object:stdClass>', $this->matcher->__toString());
    }

    /**
     * Tests that the equals matcher handles nested dependencies
     */
    public function testNestedDependencies()
    {
        $a             = new stdClass;
        $a->b          = new stdClass;
        $a->b->a       = $a;
        $this->matcher = new Phake_Matchers_EqualsMatcher($a, new \SebastianBergmann\Comparator\Factory());

        $c       = new stdClass();
        $c->b    = new stdClass();
        $c->b->a = $c;

        $c = array($c);

        $this->assertNull($this->matcher->doArgumentsMatch($c));
    }

    public function testDifferentClassObjects()
    {
        $this->matcher = new Phake_Matchers_EqualsMatcher(new PhakeTest_A(), new \SebastianBergmann\Comparator\Factory());

        $value = array(new PhakeTest_B());
        $this->setExpectedException('Exception');
        $this->matcher->doArgumentsMatch($value);
    }

    public function testArraysWithDifferentCounts()
    {
        $this->matcher = new Phake_Matchers_EqualsMatcher(array(1), new \SebastianBergmann\Comparator\Factory());

        $test = array(array(1, 2));
        $this->setExpectedException('Phake_Exception_MethodMatcherException');
        $this->matcher->doArgumentsMatch($test);
    }

    public function testArraysWithDifferentKeys()
    {
        $this->matcher = new Phake_Matchers_EqualsMatcher(array('one' => 1), new \SebastianBergmann\Comparator\Factory());

        $test = array(array('two' => 1));
        $this->setExpectedException('Exception');
        $this->matcher->doArgumentsMatch($test);
    }
}


