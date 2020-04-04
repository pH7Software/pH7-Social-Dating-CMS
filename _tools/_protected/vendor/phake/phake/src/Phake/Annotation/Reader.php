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
 * Allows reading annotations from various components
 */
class Phake_Annotation_Reader
{
    /**
     * @var ReflectionClass
     */
    private $clazz;

    /**
     * @param ReflectionClass $clazz
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ReflectionClass $clazz)
    {
        $this->clazz = $clazz;
    }

    /**
     * Returns an associative array containing a property's annotations and their values.
     *
     * @param string $property
     *
     * @return array
     */
    public function getPropertyAnnotations($property)
    {
        $property = $this->clazz->getProperty($property);

        return $this->readReflectionAnnotation($property);
    }

    /**
     * Returns an array containing the names of all properties containing a particular annotation.
     *
     * @param string $annotation
     *
     * @return array
     */
    public function getPropertiesWithAnnotation($annotation)
    {
        $properties = array();
        foreach ($this->clazz->getProperties() as $property) {
            $annotations = $this->getPropertyAnnotations($property->getName());

            if (array_key_exists($annotation, $annotations)) {
                $properties[] = $property->getName();
            }
        }
        return $properties;
    }

    /**
     * Returns all annotations for the given reflection object.
     *
     * @internal
     *
     * @param mixed $reflVar - must be an object that has the 'getDocComment' method.
     *
     * @return array
     */
    private function readReflectionAnnotation($reflVar)
    {
        $comment = $reflVar->getDocComment();

        $annotations = array();
        foreach (explode("\n", $comment) as $line) {
            if (preg_match('#^\s+\*\s*@(\w+)(?:\s+(.*))?\s*$#', $line, $matches)) {
                $annotations[$matches[1]] = isset($matches[2]) ? $matches[2] : true;
            }
        }
        return $annotations;
    }
}
