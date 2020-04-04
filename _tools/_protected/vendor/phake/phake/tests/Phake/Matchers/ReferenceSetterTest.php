<?php
/* 
 * Phake - Mocking Framework
 * 
 * Copyright (c) 2010, Mike Lively <mike.lively@sellingsource.com>
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
 * Tests the reference setter functionality.
 */
class Phake_Matchers_ReferenceSetterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Matchers_ReferenceSetter
     */
    private $setter;

    /**
     * Sets up the test fixture
     */
    public function setUp()
    {
        $this->setter = new Phake_Matchers_ReferenceSetter(42);
    }

    /**
     * Tests that reference parameter is set
     */
    public function testSettingParameter()
    {
        $value = array('');
        $this->assertNull($this->setter->doArgumentsMatch($value));

        $this->assertEquals(42, $value[0]);
    }

    /**
     * Tests that when a matcher is set on setter it will run the matcher first
     */
    public function testConditionalSetting()
    {
        $matcher = Phake::mock('Phake_Matchers_IChainableArgumentMatcher');
        $check = '';
        Phake::when($matcher)->doArgumentsMatch->thenReturnCallback(function ($arg) use (&$check) {
                $check = $arg[0];
                return true;
            });

        $this->setter->when($matcher);

        $value = array('blah');
        $this->assertNull($this->setter->doArgumentsMatch($value));
        $this->assertEquals('blah', $check);
        $this->assertEquals(42, $value[0]);
    }

    /**
     * Tests that when a matcher is set on setter it will run the matcher first
     */
    public function testConditionalSettingWontSet()
    {
        $matcher = Phake::mock('Phake_Matchers_IChainableArgumentMatcher');
        $check = '';
        Phake::when($matcher)->doArgumentsMatch->thenThrow(new Phake_Exception_MethodMatcherException());

        $this->setter->when($matcher);

        $value = array('blah');
        $this->setExpectedException('Exception');
        $this->setter->doArgumentsMatch($value);

        $this->assertEquals('blah', $value[0]);
    }

    public function testConditionalSettingFailureUpdatesMessage()
    {
        $matcher = Phake::mock('Phake_Matchers_IChainableArgumentMatcher');
        $check = '';
        Phake::when($matcher)->doArgumentsMatch->thenThrow(new Phake_Exception_MethodMatcherException("test"));

        $this->setter->when($matcher);

        $value = array('blah');

        try
        {
            $this->setter->doArgumentsMatch($value);
        }
        catch (Phake_Exception_MethodMatcherException $e)
        {
            $this->assertStringStartsWith("Failed in Phake::setReference()->when()\n", $e->getMessage(), "The methodmatcherexception is not prepended with capture info");
        }
    }

    /**
     * Tests that when returns an instance of the setter
     */
    public function testWhenReturn()
    {
        $this->assertSame($this->setter, $this->setter->when(null));
    }

    public function testToString()
    {
        $this->assertEquals('<reference parameter>', $this->setter->__toString());
    }
}


