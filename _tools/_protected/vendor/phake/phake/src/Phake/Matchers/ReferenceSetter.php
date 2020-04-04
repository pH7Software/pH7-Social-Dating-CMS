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

class Phake_Matchers_ReferenceSetter extends Phake_Matchers_SingleArgumentMatcher
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var Phake_Matchers_IChainableArgumentMatcher
     */
    private $matcher;

    /**
     * @param mixed $value The value to set the reference parameter to.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Executes the matcher on a given argument value.
     *
     * Sets the $argument to the value passed in the constructor
     *
     * @param mixed $argument
     * @throws Phake_Exception_MethodMatcherException
     */
    protected function matches(&$argument)
    {
        $args = array();
        $args[] =& $argument;
        if ($this->matcher !== null)
        {
            try
            {
                $this->matcher->doArgumentsMatch($args);
            }
            catch (Phake_Exception_MethodMatcherException $e)
            {
                throw new Phake_Exception_MethodMatcherException(trim("Failed in Phake::setReference()->when()\n" . $e->getMessage()), $e);
            }
            $this->matcher->doArgumentsMatch($args);
        }
        $argument = $this->value;
    }

    /**
     * Returns a human readable description of the argument matcher
     * @return string
     */
    public function __toString()
    {
        return '<reference parameter>';
    }

    /**
     * Assigns a matcher to the setter.
     *
     * This allows an argument to only be set if the original argument meets a specific criteria.
     *
     * The same matcher factory used by the verifier and stubber is used here.
     *
     * @param mixed $matcher
     *
     * @return Phake_Matchers_ReferenceSetter the current instance
     */
    public function when($matcher)
    {
        $factory = new Phake_Matchers_Factory();

        $this->matcher = $factory->createMatcher($matcher);

        return $this;
    }
}
