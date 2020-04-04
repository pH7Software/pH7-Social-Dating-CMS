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
 * Initializes all properties of a given object that have the @Mock annotation.
 *
 * The class can be passed to the Mock annotation or it can also read the standard @var -annotation.
 *
 * In either case the fully qualified class name should be used. The use statements are not observed.
 */
class Phake_Annotation_MockInitializer
{
    public function initialize($object)
    {
        $reflectionClass = new ReflectionClass($object);
        $reader          = new Phake_Annotation_Reader($reflectionClass);

        if ($this->useDoctrineParser()) {
            $parser = new \Doctrine\Common\Annotations\PhpParser();
        }

        $properties = $reader->getPropertiesWithAnnotation('Mock');

        foreach ($properties as $property) {
            $annotations = $reader->getPropertyAnnotations($property);

            if ($annotations['Mock'] !== true) {
                $mockedClass = $annotations['Mock'];
            } else {
                $mockedClass = $annotations['var'];
            }

            if (isset($parser)) {
                // Ignore it if the class start with a backslash
                if (substr($mockedClass, 0, 1) !== '\\') {
                    $useStatements = $parser->parseClass($reflectionClass);
                    $key           = strtolower($mockedClass);

                    if (array_key_exists($key, $useStatements)) {
                        $mockedClass = $useStatements[$key];
                    }
                }
            }

            $reflProp = new ReflectionProperty(get_class($object), $property);

            $reflProp->setAccessible(true);
            $reflProp->setValue($object, Phake::mock($mockedClass));
        }
    }

    protected function useDoctrineParser()
    {
        return version_compare(PHP_VERSION, "5.3.3", ">=") && class_exists('Doctrine\Common\Annotations\PhpParser');
    }
}
