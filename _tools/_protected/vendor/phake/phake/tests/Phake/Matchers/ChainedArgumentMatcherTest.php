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

class Phake_Matchers_ChainedArgumentMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Matchers_ChainedArgumentMatcher
     */
    private $matcher;

    /**
     * @Mock
     * @var Phake_Matchers_IArgumentMatcher
     */
    private $adapted;

    /**
     * @Mock
     * @var Phake_Matchers_IChainableArgumentMatcher
     */
    private $nextMatcher;

    public function setUp()
    {
        Phake::initAnnotations($this);

        $this->matcher = new Phake_Matchers_ChainedArgumentMatcher($this->adapted);
        $this->matcher->setNextMatcher($this->nextMatcher);
    }

    public function testMatches()
    {
        $args = array('test arg1', 'test arg2');

        Phake::when($this->adapted)->matches->thenReturn(true);
        Phake::when($this->nextMatcher)->doArgumentsMatch->thenReturn(true);

        $result = $this->matcher->doArgumentsMatch($args);

        Phake::verify($this->adapted)->matches('test arg1');
        Phake::verify($this->nextMatcher)->doArgumentsMatch(array('test arg2'));
        $this->assertNull($result);
    }

    public function testToString()
    {
        Phake::when($this->adapted)->__toString->thenReturn('test string');

        $result = $this->matcher->__toString();

        Phake::verify($this->adapted)->__toString();
        $this->assertEquals('test string', $result);
    }
}
 