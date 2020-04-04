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
 * Creates and executes the code necessary to create a mock class.
 *
 * @author Mike Lively <m@digitalsandwich.com>
 */
class Phake_ClassGenerator_MockClass
{
    private static $unsafeClasses = array('Memcached');
    /**
     * @var \Phake_ClassGenerator_ILoader
     */
    private $loader;

    private $reservedWords = array(
        'abstract' => 'abstract',
        'and' => 'and',
        'array' => 'array',
        'as' => 'as',
        'break' => 'break',
        'case' => 'case',
        'catch' => 'catch',
        'class' => 'class',
        'clone' => 'clone',
        'const' => 'const',
        'continue' => 'continue',
        'declare' => 'declare',
        'default' => 'default',
        'do' => 'do',
        'else' => 'else',
        'elseif' => 'elseif',
        'enddeclare' => 'enddeclare',
        'endfor' => 'endfor',
        'endforeach' => 'endforeach',
        'endif' => 'endif',
        'endswitch' => 'endswitch',
        'endwhile' => 'endwhile',
        'extends' => 'extends',
        'final' => 'final',
        'for' => 'for',
        'foreach' => 'foreach',
        'function' => 'function',
        'global' => 'global',
        'goto' => 'goto',
        'if' => 'if',
        'implements' => 'implements',
        'interface' => 'interface',
        'instanceof' => 'instanceof',
        'namespace' => 'namespace',
        'new' => 'new',
        'or' => 'or',
        'private' => 'private',
        'protected' => 'protected',
        'public' => 'public',
        'static' => 'static',
        'switch' => 'switch',
        'throw' => 'throw',
        'try' => 'try',
        'use' => 'use',
        'var' => 'var',
        'while' => 'while',
        'xor' => 'xor',
        'die' => 'die',
        'echo' => 'echo',
        'empty' => 'empty',
        'exit' => 'exit',
        'eval' => 'eval',
        'include' => 'include',
        'include_once' => 'include_once',
        'isset' => 'isset',
        'list' => 'list',
        'require' => 'require',
        'require_once' => 'require_once',
        'return' => 'return',
        'print' => 'print',
        'unset' => 'unset',
        '__halt_compiler' => '__halt_compiler'
    );

    /**
     * @param Phake_ClassGenerator_ILoader $loader
     */
    public function __construct(Phake_ClassGenerator_ILoader $loader = null)
    {
        if (empty($loader)) {
            $loader = new Phake_ClassGenerator_EvalLoader();
        }

        $this->loader = $loader;
    }

    /**
     * Generates a new class with the given class name
     *
     * @param string $newClassName - The name of the new class
     * @param string $mockedClassName - The name of the class being mocked
     * @param Phake_Mock_InfoRegistry $infoRegistry

     * @return NULL
     */
    public function generate($newClassName, $mockedClassName, Phake_Mock_InfoRegistry $infoRegistry)
    {
        $extends    = '';
        $implements = '';
        $interfaces = array();
        $parent = null;
        $constructor = '';

        $mockedClassNames = (array)$mockedClassName;
        $mockedClasses = array();

        foreach ($mockedClassNames as $mockedClassName)
        {
            $mockedClass = new ReflectionClass($mockedClassName);
            $mockedClasses[] = $mockedClass;

            if (!$mockedClass->isInterface()) {
                if (!empty($parent))
                {
                    throw new RuntimeException("You cannot use two classes in the same mock: {$parent->getName()}, {$mockedClass->getName()}. Use interfaces instead.");
                }
                $parent = $mockedClass;
            } else {
                if ($mockedClass->implementsInterface('Traversable') &&
                    !$mockedClass->implementsInterface('Iterator') &&
                    !$mockedClass->implementsInterface('IteratorAggregate')
                ) {
                    $interfaces[] = new ReflectionClass('Iterator');
                    if ($mockedClass->getName() != 'Traversable') {
                        $interfaces[] = $mockedClass;
                    }
                }
                else
                {
                    $interfaces[] = $mockedClass;
                }
            }
        }

       $interfaces = array_unique($interfaces);

        if (!empty($parent))
        {
            $extends = "extends {$parent->getName()}";
        }

        $interfaceNames = array_map(function (ReflectionClass $c) { return $c->getName(); }, $interfaces);
        if(($key = array_search('Phake_IMock', $interfaceNames)) !== false) {
            unset($interfaceNames[$key]);
        }
        if (!empty($interfaceNames))
        {
            $implements = ', ' . implode(',', $interfaceNames);
        }

        if (empty($parent))
        {
            $mockedClass = array_shift($interfaces);
        }
        else
        {
            $mockedClass = $parent;
        }

        $classDef = "
class {$newClassName} {$extends}
	implements Phake_IMock {$implements}
{
    public \$__PHAKE_info;

    public static \$__PHAKE_staticInfo;

	const __PHAKE_name = '{$mockedClassName}';

	public \$__PHAKE_constructorArgs;

	{$constructor}

	/**
	 * @return void
	 */
	public function __destruct() {}

 	{$this->generateSafeConstructorOverride($mockedClasses)}

	{$this->generateMockedMethods($mockedClass, $interfaces)}
}
";

        $this->loadClass($newClassName, $mockedClassName, $classDef);
        $newClassName::$__PHAKE_staticInfo = $this->createMockInfo($mockedClassName, new Phake_CallRecorder_Recorder(), new Phake_Stubber_StubMapper(), new Phake_Stubber_Answers_NoAnswer());
        $infoRegistry->addInfo($newClassName::$__PHAKE_staticInfo);
    }

    private function loadClass($newClassName, $mockedClassName, $classDef)
    {
        $isUnsafe = in_array($mockedClassName, self::$unsafeClasses);

        $oldErrorReporting = ini_get('error_reporting');
        if ($isUnsafe)
        {
            error_reporting($oldErrorReporting & ~E_STRICT);
        }
        $this->loader->loadClassByString($newClassName, $classDef);
        if ($isUnsafe)
        {
            error_reporting($oldErrorReporting);
        }
    }

    /**
     * Instantiates a new instance of the given mocked class, and configures Phake data structures on said object.
     *
     * @param string                      $newClassName
     * @param Phake_CallRecorder_Recorder $recorder
     * @param Phake_Stubber_StubMapper    $mapper
     * @param Phake_Stubber_IAnswer       $defaultAnswer
     * @param array                       $constructorArgs
     *
     * @return Phake_IMock of type $newClassName
     */
    public function instantiate(
        $newClassName,
        Phake_CallRecorder_Recorder $recorder,
        Phake_Stubber_StubMapper $mapper,
        Phake_Stubber_IAnswer $defaultAnswer,
        array $constructorArgs = null
    ) {

        $mockObject = $this->instanciateMockObject($newClassName);
        $mockObject->__PHAKE_info = $this->createMockInfo($newClassName::__PHAKE_name, $recorder, $mapper, $defaultAnswer);
        $mockObject->__PHAKE_constructorArgs = $constructorArgs;

        if (null !== $constructorArgs && method_exists($mockObject, '__construct')) {
            call_user_func_array(array($mockObject, '__construct'), $constructorArgs);
        }

        return $mockObject;
    }

    /**
     * Instantiates a new instance of the given mocked class.
     *
     * @param $newClassName
     * @return object
     */
    protected function instanciateMockObject ($newClassName) {

        $reflClass = new ReflectionClass($newClassName);
        $constructor = $reflClass->getConstructor();

        if ($constructor == null || ($constructor->class == $newClassName && $constructor->getNumberOfParameters() == 0)) {
            return new $newClassName;
        }

        if (method_exists($reflClass, "newInstanceWithoutConstructor")) {
            try {
                return $reflClass->newInstanceWithoutConstructor();
            } catch (ReflectionException $ignore) {
                /* Failed to create object, the class might be final. */
            }
        }

        if (!is_subclass_of($newClassName, "Serializable")) {
            /* Try to unserialize, this skips the constructor */
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($newClassName), $newClassName));
        }

        /* Object implements custom unserialization */
        return unserialize(sprintf('C:%d:"%s":0:{}', strlen($newClassName), $newClassName));
    }

    /**
     * Generate mock implementations of all public and protected methods in the mocked class.
     *
     * @param ReflectionClass   $mockedClass
     * @param ReflectionClass[] $mockedInterfaces
     *
     * @return string
     */
    protected function generateMockedMethods(ReflectionClass $mockedClass, array $mockedInterfaces = array(), &$implementedMethods = array())
    {
        $methodDefs = '';
        $filter     = ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PUBLIC | ~ReflectionMethod::IS_FINAL;

        if (empty($implementedMethods))
        {
            $implementedMethods = $this->reservedWords;
        }
        foreach ($mockedClass->getMethods($filter) as $method) {
            $methodName = $method->getName();
            if (!$method->isConstructor() && !$method->isDestructor() && !$method->isFinal()
                && !isset($implementedMethods[$methodName])
            ) {
                $implementedMethods[$methodName] = $methodName;
                $methodDefs .= $this->implementMethod($method, $method->isStatic()) . "\n";
            }
        }

        foreach ($mockedInterfaces as $interface) {
            $methodDefs .= $this->generateMockedMethods($interface, array(), $implementedMethods);
        }

        return $methodDefs;
    }


    private function isConstructorDefinedInInterface(ReflectionClass $mockedClass)
    {
        $constructor = $mockedClass->getConstructor();

        if (empty($constructor) && $mockedClass->hasMethod('__construct'))
        {
            $constructor = $mockedClass->getMethod('__construct');
        }

        if (empty($constructor))
        {
            return false;
        }

        $reflectionClass = $constructor->getDeclaringClass();

        if ($reflectionClass->isInterface())
        {
            return true;
        }

        /* @var ReflectionClass $interface */
        foreach ($reflectionClass->getInterfaces() as $interface)
        {
            if ($interface->getConstructor() !== null || $interface->hasMethod('__construct'))
            {
                return true;
            }
        }

        $parent = $reflectionClass->getParentClass();
        if (!empty($parent))
        {
            return $this->isConstructorDefinedInInterface($parent);
        }
        else
        {
            return false;
        }
    }

    private function isConstructorDefinedAndFinal(ReflectionClass $mockedClass)
    {
        $constructor = $mockedClass->getConstructor();
        if (!empty($constructor) && $constructor->isFinal()) {
            return true;
        }

        return false;
    }

    private function generateSafeConstructorOverride(array $mockedClasses)
    {
        $overrideConstructor = true;

        foreach ($mockedClasses as $class)
        {
            $overrideConstructor = $overrideConstructor
                && !$this->isConstructorDefinedAndFinal($class)
                && !$this->isConstructorDefinedInInterface($class);

            if (!$class->isInterface())
            {
                $realClass = $class;
            }
        }
        if ($overrideConstructor && !empty($realClass))
        {
            $constructorDef = "
	public function __construct()
	{
	    {$this->getConstructorChaining($realClass)}
	}
";
            return $constructorDef;
        }
        else
        {
            return '';
        }
    }


    /**
     * Creates the constructor implementation
     *
     * @param ReflectionClass $originalClass
     * @return string
     */
    protected function getConstructorChaining(ReflectionClass $originalClass)
    {
        return $originalClass->hasMethod('__construct') ? "

		if (is_array(\$this->__PHAKE_constructorArgs))
		{
			call_user_func_array(array(\$this, 'parent::__construct'), \$this->__PHAKE_constructorArgs);
			\$this->__PHAKE_constructorArgs = null;
		}
		" : "";
    }

    /**
     * Creates the implementation of a single method
     *
     * @param ReflectionMethod $method
     *
     * @return string
     */
    protected function implementMethod(ReflectionMethod $method, $static = false)
    {
        $modifiers = implode(
            ' ',
            Reflection::getModifierNames($method->getModifiers() & ~ReflectionMethod::IS_ABSTRACT)
        );

        $reference = $method->returnsReference() ? '&' : '';

        if ($static)
        {
            $context = '__CLASS__';
        }
        else
        {
            $context = '$this';
        }

        $returnHint = '';
        $nullReturn = 'null';
        $resultReturn = '$__PHAKE_result';
        if (method_exists($method, 'hasReturnType') && $method->hasReturnType())
        {
            $returnType = $method->getReturnType();
            $returnHint = ' : ' . $returnType;

            if ($returnType == 'void')
            {
                $nullReturn = '';
                $resultReturn = '';
            }
        }

        $docComment = $method->getDocComment() ?: '';
        $methodDef = "
	{$docComment}
	{$modifiers} function {$reference}{$method->getName()}({$this->generateMethodParameters($method)}){$returnHint}
	{
		\$__PHAKE_args = array();
		{$this->copyMethodParameters($method)}

        \$__PHAKE_info = Phake::getInfo({$context});
		if (\$__PHAKE_info === null) {
		    return {$nullReturn};
		}

		\$__PHAKE_funcArgs = func_get_args();
		\$__PHAKE_answer = \$__PHAKE_info->getHandlerChain()->invoke({$context}, '{$method->getName()}', \$__PHAKE_funcArgs, \$__PHAKE_args);

	    \$__PHAKE_callback = \$__PHAKE_answer->getAnswerCallback({$context}, '{$method->getName()}');

	    if (\$__PHAKE_callback instanceof Phake_Stubber_Answers_ParentDelegateCallback)
	    {
    	    \$__PHAKE_result = \$__PHAKE_callback(\$__PHAKE_args);
	    }
	    else
	    {
    	    \$__PHAKE_result = call_user_func_array(\$__PHAKE_callback, \$__PHAKE_args);
	    }
	    \$__PHAKE_answer->processAnswer(\$__PHAKE_result);
	    return {$resultReturn};
	}
";

        return $methodDef;
    }

    /**
     * Generates the code for all the parameters of a given method.
     *
     * @param ReflectionMethod $method
     *
     * @return string
     */
    protected function generateMethodParameters(ReflectionMethod $method)
    {
        $parameters = array();
        foreach ($method->getParameters() as $parameter) {
            $parameters[] = $this->implementParameter($parameter);
        }

        return implode(', ', $parameters);
    }

    /**
     * Generates the code for all the parameters of a given method.
     *
     * @param ReflectionMethod $method
     *
     * @return string
     */
    protected function copyMethodParameters(ReflectionMethod $method)
    {
        $copies = "\$funcGetArgs = func_get_args();\n\t\t\$__PHAKE_numArgs = count(\$funcGetArgs);\n\t\t";
        $variadicParameter = false;
        $parameterCount = count($method->getParameters());
        foreach ($method->getParameters() as $parameter) {
            $pos = $parameter->getPosition();
            if (method_exists($parameter, 'isVariadic') && $parameter->isVariadic()) {
                $parameterCount--;
                $variadicParameter = $parameter->getName();
                break;
            }
            else {
                $copies .= "if ({$pos} < \$__PHAKE_numArgs) \$__PHAKE_args[] =& \${$parameter->getName()};\n\t\t";
            }
        }

        if ($variadicParameter)
        {
            $copies .= "for (\$__PHAKE_i = " . $parameterCount . "; \$__PHAKE_i < \$__PHAKE_numArgs; \$__PHAKE_i++) \$__PHAKE_args[] =& \${$variadicParameter}[\$__PHAKE_i - $parameterCount];\n\t\t";
        }
        else
        {
            $copies .= "for (\$__PHAKE_i = " . $parameterCount . "; \$__PHAKE_i < \$__PHAKE_numArgs; \$__PHAKE_i++) \$__PHAKE_args[] = func_get_arg(\$__PHAKE_i);\n\t\t";
        }

        return $copies;
    }

    /**
     * Generates the code for an individual method parameter.
     *
     * @param ReflectionParameter $parameter
     *
     * @return string
     */
    protected function implementParameter(ReflectionParameter $parameter)
    {
        $default = '';
        $type    = '';

        try
        {
            if ($parameter->isArray()) {
                $type = 'array ';
            } elseif (method_exists($parameter, 'isCallable') && $parameter->isCallable()) {
                $type = 'callable ';
            } elseif ($parameter->getClass() !== null) {
                $type = $parameter->getClass()->getName() . ' ';
            } elseif (method_exists($parameter, 'hasType') && $parameter->hasType())
            {
                $type = $parameter->getType() . ' ';
            }
        }
        catch (ReflectionException $e)
        {
            //HVVM is throwing an exception when pulling class name when said class does not exist
            if (!defined('HHVM_VERSION'))
            {
                throw $e;
            }
        }

        $variadic = '';
        if ($parameter->isDefaultValueAvailable()) {
            $default = ' = ' . var_export($parameter->getDefaultValue(), true);
        } elseif (method_exists($parameter, 'isVariadic') && $parameter->isVariadic()) {
            $variadic = '...';
        } elseif ($parameter->isOptional()) {
            $default = ' = null';
        }

        return $type . ($parameter->isPassedByReference() ? '&' : '') . $variadic . '$' . $parameter->getName() . $default;
    }

    /**
     * @param $newClassName
     * @param Phake_CallRecorder_Recorder $recorder
     * @param Phake_Stubber_StubMapper $mapper
     * @param Phake_Stubber_IAnswer $defaultAnswer
     * @return Phake_Mock_Info
     */
    private function createMockInfo(
        $className,
        Phake_CallRecorder_Recorder $recorder,
        Phake_Stubber_StubMapper $mapper,
        Phake_Stubber_IAnswer $defaultAnswer
    ) {
        $info = new Phake_Mock_Info($className, $recorder, $mapper, $defaultAnswer);

        $info->setHandlerChain(
            new Phake_ClassGenerator_InvocationHandler_Composite(array(
                new Phake_ClassGenerator_InvocationHandler_FrozenObjectCheck($info),
                new Phake_ClassGenerator_InvocationHandler_CallRecorder($info->getCallRecorder()),
                new Phake_ClassGenerator_InvocationHandler_MagicCallRecorder($info->getCallRecorder()),
                new Phake_ClassGenerator_InvocationHandler_StubCaller($info->getStubMapper(), $info->getDefaultAnswer(
                )),
            ))
        );

        $info->getStubMapper()->mapStubToMatcher(
            new Phake_Stubber_AnswerCollection(new Phake_Stubber_Answers_StaticAnswer('Mock for ' . $info->getName())),
            new Phake_Matchers_MethodMatcher('__toString', null)
        );

        return $info;
    }
}
