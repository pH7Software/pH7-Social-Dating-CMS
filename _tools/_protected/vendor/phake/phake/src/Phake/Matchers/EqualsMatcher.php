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
 * A matcher to validate that an argument equals a particular value.
 *
 * This matcher utilizes the same functionality as non-strict equality in php, in other words '=='
 */
class Phake_Matchers_EqualsMatcher extends Phake_Matchers_SingleArgumentMatcher
{
    /**
     * @var mixed
     */
    private $value;

    /**\
     * @var \SebastianBergmann\Comparator\Factory
     */
    private $comparatorFactory;

    /**
     * Pass in the value that the upcoming arguments is expected to equal.
     *
     * @param mixed $value
     * @param \SebastianBergmann\Comparator\Factory $comparatorFactory
     */
    public function __construct($value, \SebastianBergmann\Comparator\Factory $comparatorFactory)
    {
        $this->value = $value;
        $this->comparatorFactory = $comparatorFactory;
    }

    /**
     * Returns whether or not the passed argument matches the matcher.
     */
    protected  function matches(&$argument)
    {
        try
        {
            $compare = $this->comparatorFactory->getComparatorFor($this->value, $argument);
            $compare->assertEquals($this->value, $argument);
        }
        catch (\SebastianBergmann\Comparator\ComparisonFailure $e)
        {
            throw new Phake_Exception_MethodMatcherException(trim($e->getMessage() . "\n" . $e->getDiff()), $e);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $converter = new Phake_String_Converter();
        return "equal to {$converter->convertToString($this->value)}";
    }
}
