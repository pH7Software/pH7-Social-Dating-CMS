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
 * Acts as a proxy to Phake_CallRecorder_Verifier that allows verifying methods using the magic
 * __call() method in PHP.
 *
 * Also throws an exception when a verification call fails.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Proxies_VerifierProxy
{
    /**
     * @var Phake_CallRecorder_Verifier
     */
    private $verifier;

    /**
     * @var Phake_Matchers_Factory
     */
    private $matcherFactory;

    /**
     * @var Phake_CallRecorder_IVerifierMode
     */
    private $mode;

    /**
     *
     * @var Phake_Client_IClient
     */
    private $client;

    /**
     * @param Phake_CallRecorder_Verifier      $verifier
     * @param Phake_Matchers_Factory           $matcherFactory
     * @param Phake_CallRecorder_IVerifierMode $mode
     * @param Phake_Client_IClient             $client
     */
    public function __construct(
        Phake_CallRecorder_Verifier $verifier,
        Phake_Matchers_Factory $matcherFactory,
        Phake_CallRecorder_IVerifierMode $mode,
        Phake_Client_IClient $client
    ) {
        $this->verifier       = $verifier;
        $this->matcherFactory = $matcherFactory;
        $this->mode           = $mode;
        $this->client         = $client;
    }

    /**
     * A call magic method to provide a more fluent interface to the verifier.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Phake_CallRecorder_VerifierResult
     */
    public function __call($method, array $arguments)
    {
        $expectation = new Phake_CallRecorder_CallExpectation($this->verifier->getObject(
        ), $method, $this->matcherFactory->createMatcherChain($arguments), $this->mode);

        $result = $this->verifier->verifyCall($expectation);

        return $this->client->processVerifierResult($result);
    }

    /**
     * A magic call to verify a call with any parameters.
     *
     * @param string $method
     *
     * @throws InvalidArgumentException if $method is not a valid parameter/method name
     *
     * @return Phake_CallRecorder_VerifierResult
     */
    public function __get($method)
    {
        $obj = $this->verifier->getObject();

        if (is_string($method) && ctype_digit($method[0])) {
            throw new InvalidArgumentException('String parameter to __get() cannot start with an integer');
        }

        if (!is_string($method) && !is_object($method)) {
            $message = sprintf('Parameter to __get() must be a string, %s given', gettype($method));
            throw new InvalidArgumentException($message);
        }

        if (method_exists($obj, '__get') && !(is_string($method) && method_exists($obj, $method))) {
            return $this->__call('__get', array($method));
        }

        return $this->__call($method, array(new Phake_Matchers_AnyParameters));
    }
}
