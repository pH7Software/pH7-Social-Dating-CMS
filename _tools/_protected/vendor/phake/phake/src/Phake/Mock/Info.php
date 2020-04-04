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

/**
 * Stores data about the mock object.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_Mock_Info {
    private $uniqId;

    private $recorder;

    private $mapper;

    private $answer;

    private $frozen;

    private $name;

    /**
     * @var Phake_ClassGenerator_InvocationHandler_IInvocationHandler
     */
    private $handlerChain;

    public function __construct($name, Phake_CallRecorder_Recorder $recorder, Phake_Stubber_StubMapper $mapper, Phake_Stubber_IAnswer $defaultAnswer)
    {
        $this->uniqId = uniqid('', true);
        $this->recorder = $recorder;
        $this->mapper = $mapper;
        $this->answer = $defaultAnswer;
        $this->frozen = false;
        $this->name = $name;
    }

    public function getCallRecorder()
    {
        return $this->recorder;
    }

    public function getStubMapper()
    {
        return $this->mapper;
    }

    public function getDefaultAnswer()
    {
        return $this->answer;
    }

    public function isObjectFrozen()
    {
        return $this->frozen;
    }

    public function freezeObject()
    {
        $this->frozen = true;
    }

    public function thawObject()
    {
        $this->frozen = false;
    }

    public function getHandlerChain()
    {
        return $this->handlerChain;
    }

    public function setHandlerChain(Phake_ClassGenerator_InvocationHandler_IInvocationHandler $handlerChain)
    {
        $this->handlerChain = $handlerChain;
    }

    public function getName()
    {
        return $this->name;
    }

    public function resetInfo()
    {
        $this->thawObject();
        $this->mapper->removeAllAnswers();
        $this->recorder->removeAllCalls();
    }
}