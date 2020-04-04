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
 * Determines if a method and argument matchers match a given method call.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Matchers_MethodMatcher implements Phake_Matchers_IMethodMatcher
{
    /**
     * @var string
     */
    private $expectedMethod;

    /**
     * @var Phake_Matchers_IChainableArgumentMatcher
     */
    private $argumentMatcherChain;

    public function __construct($expectedMethod, Phake_Matchers_IChainableArgumentMatcher $argumentMatcherChain = null)
    {
        $this->expectedMethod   = $expectedMethod;
        $this->argumentMatcherChain = $argumentMatcherChain;
    }

    /**
     * Determines if the given method and arguments match the configured method and argument matchers
     * in this object. Returns true on success, false otherwise.
     *
     * @param string $method
     * @param array  $args
     *
     * @return boolean
     */
    public function matches($method, array &$args)
    {
        try
        {
            $this->assertMatches($method, $args);
            return true;
        }
        catch (Phake_Exception_MethodMatcherException $e)
        {
            return false;
        }
    }

    /**
     * Asserts whether or not the given method and arguments match the configured method and argument matchers in this \
     * object.
     *
     * @param string $method
     * @param array $args
     * @return bool
     * @throws Phake_Exception_MethodMatcherException
     */
    public function assertMatches($method, array &$args)
    {
        if ($this->expectedMethod != $method)
        {
            throw new Phake_Exception_MethodMatcherException("Expected method {$this->expectedMethod} but received {$method}");
        }

        $this->doArgumentsMatch($args);
    }

    /**
     * Determines whether or not given arguments match the argument matchers configured in the object.
     *
     * Throws an exception with a description if the arguments do not match.
     *
     * @param array $args
     * @return bool
     * @throws Phake_Exception_MethodMatcherException
     */
    private function doArgumentsMatch(array &$args)
    {
        if ($this->argumentMatcherChain !== null)
        {
            try
            {
                $this->argumentMatcherChain->doArgumentsMatch($args);
            }
            catch (Phake_Exception_MethodMatcherException $e)
            {
                $position = $e->getArgumentPosition() + 1;
                throw new Phake_Exception_MethodMatcherException(trim("Argument #{$position} failed test\n" . $e->getMessage()), $e);
            }
        }
        elseif (count($args) != 0)
        {
            throw new Phake_Exception_MethodMatcherException("No matchers were given to Phake::when(), but arguments were received by this method.");
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->expectedMethod;
    }
}
