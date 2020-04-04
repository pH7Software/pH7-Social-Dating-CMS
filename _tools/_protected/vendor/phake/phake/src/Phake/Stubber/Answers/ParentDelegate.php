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
 * An answer delegate that allows mocked methods to call their parent methods.
 *
 * If a particular method does not have a parent (ie abstract methods) then a static null answer (effectively) is used
 * instead.
 *
 * This class is both the delegator and the delegate.
 */
class Phake_Stubber_Answers_ParentDelegate implements Phake_Stubber_IAnswer
{
    private $capturedReturn;

    public function __construct(&$captor = null)
    {
        $this->capturedReturn =& $captor;
    }

    public function processAnswer($answer)
    {
        $this->capturedReturn = $answer;
    }

    public function getAnswerCallback($context, $method)
    {
        $fallback =  array($this, "getFallback");
        try
        {
            $reflClass = new ReflectionClass($context);
            $reflParent = $reflClass->getParentClass();

            if (!is_object($reflParent))
            {
                return $fallback;
            }

            $reflMethod = $reflParent->getMethod($method);

            if (!$reflMethod->isAbstract())
            {
                if (defined('HHVM_VERSION'))
                {
                    return array('parent', $method);
                }
                else
                {
                    return new Phake_Stubber_Answers_ParentDelegateCallback($context, $reflMethod);
                }
            }
        }
        catch (ReflectionException $e)
        {
        }
        return $fallback;
    }

    public function getFallback()
    {
        return null;
    }
}


