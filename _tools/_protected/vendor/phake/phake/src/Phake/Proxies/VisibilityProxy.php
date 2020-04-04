<?php
/* 
 * Phake - Mocking Framework
 * 
 * Copyright (c) 2010-2015, Mike Lively <m@digitalsandwich.com>
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
 * Acts as a proxy to any object that allows calling any private or protected method on the wrapper and forward those
 * calls to the wrapped object.
 *
 * I debated alot about putting anything like this in Phake and at some point I may just pull it out into its own
 * library. It should be used with great caution and should really only need to be used to help ease steps in
 * refactoring of otherwise hard to reach private and protected methods
 *
 * @author Mike Lively <m@digitalsandwich.com>
 * @internal This class will quite likely change soon, don't use it outside of phake code
 */
class Phake_Proxies_VisibilityProxy
{
    private $proxied;

    public function __construct($proxied)
    {
        if (!is_object($proxied))
        {
            throw new InvalidArgumentException("Phake_Proxies_VisibilityProxy was passed a non-object");
        }
        $this->proxied = $proxied;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->proxied, $method))
        {
            $reflMethod = new ReflectionMethod(get_class($this->proxied), $method);
            $reflMethod->setAccessible(true);
            return $reflMethod->invokeArgs($this->proxied, $arguments);
        }
        elseif (method_exists($this->proxied, '__call'))
        {
            $reflMethod = new ReflectionMethod(get_class($this->proxied), '__call');
            return $reflMethod->invokeArgs($this->proxied, func_get_args());
        }
        else
        {
            throw new InvalidArgumentException("Method {$method} does not exist on " . get_class($this->proxied) . '.');
        }
    }
}
