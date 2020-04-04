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
 * A proxy to handle verifying various calls to the magic __call method
 *
 * The parameters that you would like to verify are passed into the constructor.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Proxies_CallVerifierProxy
{
    /**
     * @var Phake_Matchers_IChainableArgumentMatcher
     */
    private $argumentMatcher;

    /**
     * @var Phake_Client_IClient
     */
    private $client;

    /**
     * @var bool
     */
    private $static;

    /**
     * @param Phake_Matchers_IChainableArgumentMatcher $argumentMatcher
     * @param Phake_Client_IClient $client
     * @param bool $static
     */
    public function __construct(Phake_Matchers_IChainableArgumentMatcher $argumentMatcher = null, Phake_Client_IClient $client, $static)
    {
        $this->argumentMatcher  = $argumentMatcher;
        $this->client     = $client;
        $this->static = $static;
    }


    /**
     * Verifies that the call to __call was made on the given object with the parameters passed into the constructor
     *
     * @param Phake_IMock                      $obj
     * @param Phake_CallRecorder_IVerifierMode $verifierMode
     *
     * @return array
     */
    public function isCalledOn(Phake_IMock $obj, Phake_CallRecorder_IVerifierMode $verifierMode = null)
    {
        if ($verifierMode === null) {
            $verifierMode = new Phake_CallRecorder_VerifierMode_Times(1);
        }

        $context = $this->static ? get_class($obj) : $obj;
        $call = $this->static ? '__callStatic' : '__call';

        $verifier    = new Phake_CallRecorder_Verifier(Phake::getInfo($context)->getCallRecorder(), $obj);
        $expectation = new Phake_CallRecorder_CallExpectation($context, $call, $this->argumentMatcher, $verifierMode);
        $result      = $verifier->verifyCall($expectation);

        return $this->client->processVerifierResult($result);
    }
}
