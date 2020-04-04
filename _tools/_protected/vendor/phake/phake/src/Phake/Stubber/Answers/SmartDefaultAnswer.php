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
 * Returns the proper default value for a method based on the return type.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Stubber_Answers_SmartDefaultAnswer implements Phake_Stubber_IAnswer
{
    public function processAnswer($answer)
    {

    }

    public function getAnswerCallback($context, $method)
    {
        $class = new ReflectionClass($context);
        $method = $class->getMethod($method);

        $defaultAnswer = null;

        if (method_exists($method, 'hasReturnType') && $method->hasReturnType())
        {
            switch ((string)$method->getReturnType())
            {
                case 'int':
                    $defaultAnswer = 0;
                    break;
                case 'float':
                    $defaultAnswer = 0.0;
                    break;
                case 'string':
                    $defaultAnswer = "";
                    break;
                case 'bool':
                    $defaultAnswer = false;
                    break;
                case 'array':
                    $defaultAnswer = array();
                    break;
                case 'callable':
                    $defaultAnswer = function () {};
                    break;
                default:
                    if (class_exists((string)$method->getReturnType()))
                    {
                        $defaultAnswer = Phake::mock((string)$method->getReturnType());
                    }
                    break;
            }
        }

        return function () use ($defaultAnswer)
        {
            return $defaultAnswer;
        };
    }
}


