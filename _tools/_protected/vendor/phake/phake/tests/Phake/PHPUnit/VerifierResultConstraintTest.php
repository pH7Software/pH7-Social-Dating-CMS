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

class Phake_PHPUnit_VerifierResultConstraintTest extends PHPUnit_Framework_TestCase
{
    private $constraint;

    public function setUp()
    {
        if (version_compare('3.6.0', PHPUnit_Runner_Version::id()) != 1) {
            $this->markTestSkipped('The tested class is not compatible with current version of PHPUnit.');
        }
        $this->constraint = new Phake_PHPUnit_VerifierResultConstraint($this->verifier);
    }

    public function testExtendsPHPUnitConstraint()
    {
        $this->assertInstanceOf('PHPUnit_Framework_Constraint', $this->constraint);
    }

    public function testEvaluateReturnsTrueIfVerifyResultIsTrue()
    {
        $result = new Phake_CallRecorder_VerifierResult(true, array());
        $this->assertTrue($this->constraint->evaluate($result));
    }

    public function testEvaluateReturnsFalseWhenVerifierReturnsFalse()
    {
        $result = new Phake_CallRecorder_VerifierResult(false, array());
        $this->assertFalse($this->constraint->evaluate($result));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testEvaluateThrowsWhenArgumentIsNotAResult()
    {
        $this->constraint->evaluate('');
    }

    public function testToString()
    {
        $this->assertEquals('is called', $this->constraint->toString());
    }

    public function testCustomFailureDescriptionReturnsDescriptionFromResult()
    {
        $result = new Phake_CallRecorder_VerifierResult(false, array(), "The call failed!");

        try {
            $this->constraint->fail($result, '');
            $this->fail('expected an exception to be thrown');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('The call failed!', $e->getDescription());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFailThrowsWhenArgumentIsNotAResult()
    {
        $this->constraint->fail('', '');
    }
}

