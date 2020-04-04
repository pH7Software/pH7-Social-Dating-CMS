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
 * Tests the Call Recorder Call value object
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_CallRecorder_CallTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_CallRecorder_Call
     */
    private $call;

    /**
     * @var Phake_CallRecorder_Call
     */
    private $staticCall;

    private $mock;

    public function setUp()
    {
        $this->mock = Phake::mock('Phake_IMock');

        $this->call = new Phake_CallRecorder_Call($this->mock, 'someMethod', array('foo', 'bar'));
        $this->staticCall = new Phake_CallRecorder_Call(get_class($this->mock), 'someMethod', array('foo', 'bar'));
    }

    /**
     * Tests getObject()
     */
    public function testGetObject()
    {
        $this->assertSame($this->mock, $this->call->getObject());
        $this->assertSame(get_class($this->mock), $this->staticCall->getObject());
    }

    /**
     * Tests getMethod()
     */
    public function testGetMethod()
    {
        $this->assertEquals('someMethod', $this->call->getMethod());
        $this->assertEquals('someMethod', $this->staticCall->getMethod());
    }

    public function testGetArguments()
    {
        $this->assertEquals(array('foo', 'bar'), $this->call->getArguments());
        $this->assertEquals(array('foo', 'bar'), $this->staticCall->getArguments());
    }

    public function testToString()
    {
        $this->assertEquals('Phake_IMock->someMethod(<string:foo>, <string:bar>)', $this->call->__toString());
        $this->assertEquals('Phake_IMock::someMethod(<string:foo>, <string:bar>)', $this->staticCall->__toString());
    }

    public function testToStringOnAllArgumentTypes()
    {
        $call = new Phake_CallRecorder_Call($this->mock, 'someMethod', array(
            new stdClass,
            array(),
            null,
            opendir('.'),
            'foo',
            42,
            true
        ));
        $this->assertEquals(
            'Phake_IMock->someMethod(<object:stdClass>, <array>, <null>, <resource>, <string:foo>, <integer:42>, <boolean:true>)',
            $call->__toString()
        );
    }
}


