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
 * Records calls made to particular objects.
 *
 * It is assumed that calls will be recorded in the order that they are made.
 *
 * Provides methods to playback calls again in order.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_CallRecorder_Recorder
{
    /**
     * @var array
     */
    private $calls = array();

    /**
     * @var array
     */
    private $positions = array();

    /**
     * @var array
     */
    private $unverifiedCalls = array();

    /**
     * @var int
     */
    private static $lastPosition = 0;

    /**
     * Records that a given
     *
     * @param Phake_CallRecorder_Call $call
     */
    public function recordCall(Phake_CallRecorder_Call $call)
    {
        $this->calls[]                           = $call;
        $this->positions[spl_object_hash($call)] = new Phake_CallRecorder_Position(self::$lastPosition++);
        $this->unverifiedCalls[spl_object_hash($call)] = $call;
    }

    /**
     * Returns all calls recorded in the order they were recorded.
     * @return array
     */
    public function getAllCalls()
    {
        return $this->calls;
    }

    /**
     * Removes all calls from the call recorder.
     *
     * Also removes all positions
     */
    public function removeAllCalls()
    {
        $this->calls     = array();
        $this->positions = array();
    }

    /**
     * Retrieves call info for a particular call
     *
     * @param Phake_CallRecorder_Call $call
     *
     * @return Phake_CallRecorder_CallInfo
     */
    public function getCallInfo(Phake_CallRecorder_Call $call)
    {
        if (in_array($call, $this->calls, true)) {
            return new Phake_CallRecorder_CallInfo($call, $this->positions[spl_object_hash($call)]);
        } else {
            return null;
        }
    }

    /**
     * Marks an individual call as being verified
     *
     * @param Phake_CallRecorder_Call $call
     */
    public function markCallVerified(Phake_CallRecorder_Call $call)
    {
        unset($this->unverifiedCalls[spl_object_hash($call)]);
    }

    /**
     * Returns all unverified calls from the recorder
     *
     * @return array
     */
    public function getUnverifiedCalls()
    {
        return array_values($this->unverifiedCalls);
    }
}
