.. _answers:

*******
Answers
*******

In all of the examples so far, the ``thenReturn()`` answer is being used. There are other answers that are remarkably
useful writing your tests.

Throwing Exceptions
===================

Exception handling is a common aspect of most object oriented systems that should be tested. The key to being able to
test your exception handling is to be able to control the throwing of your exceptions. Phake allows this using the
``thenThrow()`` answer. This answer allows you to throw a specific exception from any mocked method. Below is an
example of a piece of code that catches an exception from the method foo() and then logs a message with the exception
message.

.. code-block:: php

    class MyClass
    {
        private $logger;

        public function __construct(LOGGER $logger)
        {
            $this->logger = $logger;
        }

        public function processSomeData(MyDataProcessor $processor, MyData $data)
        {
            try
            {
                $processor->process($data);
            }
            catch (Exception $e)
            {
                $this->logger->log($e->getMessage());
            }
        }
    }

In order to test this we must mock ``foo()`` so that it throws an exception when it is called. Then we can verify that
``log()`` is called with the appropriate message.

.. code-block:: php

    class MyClassTest extends PHPUnit_Framework_TestCase
    {
        public function testProcessSomeDataLogsExceptions()
        {
            $logger = Phake::mock('LOGGER');
            $data = Phake::mock('MyData');
            $processor = Phake::mock('MyDataProcessor');

            Phake::when($processor)->process($data)->thenThrow(new Exception('My error message!'));

            $sut = new MyClass($logger);
            $sut->processSomeData($processor, $data);

            //This comes from the exception we created above
            Phake::verify($logger)->log('My error message!');
        }
    }

.. _then-call-parent:
Calling the Parent
==================

Phake provides the ability to allow calling the actual method of an object on a method by method
basis by using the ``thenCallParent()`` answer. This will result in the actual method being called.
Consider the following class.

.. code-block:: php

    class MyClass
    {
        public function foo()
        {
            return '42';
        }
    }

The ``thenCallParent()`` answer can be used here to ensure that the actual method in the class is
called resulting in the value 42 being returned from calls to that mocked method.

.. code-block:: php

    class MyClassTest extends PHPUnit_Framework_TestCase
    {
        public function testCallingParent()
        {
            $mock = Phake::mock('MyClass');
            Phake::when($mock)->foo()->thenCallParent();

            $this->assertEquals(42, $mock->foo());
        }
    }

Please avoid using this answer as much as possible especially when testing newly written code. If you find yourself
requiring a class to be only partially mocked then that is a code smell for a class that is likely doing too much. An
example of when this is being done is why you are testing a class that has a singular method that has a lot of side
effects that you want to mock while you allow the other methods to be called as normal. In this case that method that
you are desiring to mock should belong to a completely separate class. It is obvious by the very fact that you are able
to mock it without needing to mock other messages that it performs a different function.

Even though partial mocking should be avoided with new code, it is often very necessary to allow creating tests while
refactoring legacy code, tests involving 3rd party code that can’t be changed, or new tests of already written code
that cannot yet be changed. This is precisely the reason why this answer exists and is also why it is not the default
answer in Phake.

Capturing a Return Value
========================

Another tool in Phake for testing legacy code is the ``captureReturnTo()`` answer. This performs a function similar to
argument capturing, however it instead captures what the actual method of a mock object returns to the variable passed
as its parameter. Again, this should never be needed if you are testing newly written code. However I have ran across
cases several times where legacy code calls protected factory methods and the result of the method call is never
exposed. This answer gives you a way to access that variable to ensure that the factory was called and is operating
correctly in the context of your method that is being tested.

Answer Callbacks
================

While the answers provided in Phake should be able to cover most of the scenarios you will run into when using mocks in
your unit tests there may occasionally be times when you need more control over what is returned from your mock
methods. When this is the case, you can use a callback answer. These do generally increase the complexity of tests and
you really should only use them if you won't know what you need to return until call time.

You can specify a callback answer using the thenReturnCallback method. This argument takes a callback or a closure.
The callback will be passed the same arguments as were passed to the method being stubbed. This allows you to use them
to help determine the answer.


.. code-block:: php

    class MyClassTest extends PHPUnit_Framework_TestCase
    {
        public function testCallback()
        {
            $mock = Phake::mock('MyClass');
            Phake::when($mock)->foo->thenReturnCallback(function ($val) { return $val * 2; });

            $this->assertEquals(42, $mock->foo(21));
        }
    }

Custom Answers
==============

You can also create custom answers. All answers in Phake implement the ``Phake_Stubber_IAnswer`` interface. This
interface defines a single method called ``getAnswer()`` that can be used to return what will be returned from a call
to the method being stubbed. If you need to get access to how the method you are stubbing was invoked, there is a more
complex set of interfaces that can be implemented: ``Phake_Stubber_Answers_IDelegator`` and
``Phake_Stubber_IAnswerDelegate``.

``Phake_Stubber_Answers_IDelegator`` extends ``Phake_Stubber_IAnswer`` and defines an additional method called
``processAnswer()`` that is used to perform processing on the results of ``getAnswer()`` prior to passing it on to the
stub’s caller. ``Phake_Stubber_IAnswerDelegate`` defines an interface that allows you to create a callback that is
called to generate the answer from the stub. It defines ``getCallBack()`` which allows you to generate a PHP callback
based on the object, method, and arguments that a stub was called with. It also defines ``getArguments()`` which allows
you to generate the arguments that will be passed to the callback based on the method name and arguments the stub was
called with.
