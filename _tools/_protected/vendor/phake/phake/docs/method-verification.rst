.. _method-verification-section:

*******************
Method Verification
*******************

The ``Phake::verify()`` method is used to assert that method calls have been
made on a mock object that you can create with ``Phake::mock()``.
``Phake::verify()`` accepts the mock object you want to verify calls against.
Mock objects in Phake can almost be viewed as a tape recorder. Any time the code you are testing
calls a method on an object you create with ``Phake::mock()`` it is going to
record the method that you called along with all of the parameters used to call that method. Then
``Phake::verify()`` will look at that recording and allow you to assert whether
or not a certain call was made.

.. code-block:: php

    class PhakeTest1 extends PHPUnit_Framework_TestCase
    {
        public function testBasicVerify()
        {
            $mock = Phake::mock('MyClass');

            $mock->foo();

            Phake::verify($mock)->foo();
        }
    }

The ``Phake::verify()`` call here, verifies that the method ``foo()`` has been called once (and only once) with no
parameters on the object ``$mock``. A very important thing to note here that is a departure from most (if not all)
other PHP mocking frameworks is that you want to verify the method call AFTER the method call takes place. Other
mocking frameworks such as the one built into PHPUnit depend on you setting the expectations of what will get called
prior to running the system under test.

Phake strives to allow you to follow the four phases of a unit test as laid out in xUnit Test Patterns: setup,
exercise, verify, and teardown. The setup phase of a test using Phake for mocking will now include calls to
``Phake::mock()`` for each class you want to mock. The exercise portion of your code will remain the same. The verify
section of your code will include calls to ``Phake::verify()``. The exercise and teardown phases will remain unchanged.

Verifying Method Parameters
===========================

Verifying method parameters using Phake is very simple yet can be very flexible. There are a wealth of options for
matching parameters that are discussed later on in :ref:`method-parameter-matchers-section`.

Verifying Multiple Invocations
==============================

A common need for mock objects is the ability to have variable multiple invocations on that object. Phake allows you to
use ``Phake::verify()`` multiple times on the same object. A notable difference between Phake and PHPUnit’s mocking
framework is the ability to mock multiple invocations of the same method with no regard for call sequences. The PHPUnit
mocking test below would fail for this reason.

.. code-block:: php

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testPHPUnitMock()
        {
            $mock = $this->getMock('PhakeTest_MockedClass');

            $mock->expects($this->once())->method('fooWithArgument')
                    ->with('foo');

            $mock->expects($this->once())->method('fooWithArgument')
                    ->with('bar');

            $mock->fooWithArgument('foo');
            $mock->fooWithArgument('bar');
        }
    }

The reason this test fails is because by default PHPUnit only allows a single expectation per method. The way you can
fix this is by using the `at()` matcher. This allows you to specify the index of the invocation you want to match
again. So to make the test above work you would have to change it.

.. code-block:: php

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testPHPUnitMock()
        {
            $mock = $this->getMock('PhakeTest_MockedClass');

            //NOTICE this is now at() instead of once()
            $mock->expects($this->at(0))->method('fooWithArgument')
                    ->with('foo');

            //NOTICE this is now at() instead of once()
            $mock->expects($this->at(1))->method('fooWithArgument')
                    ->with('bar');

            $mock->fooWithArgument('foo');
            $mock->fooWithArgument('bar');
        }
    }

This test will now run as expected. There is still one small problem however and that is that you are now testing not
just the invocations but also the order of invocations. Many times the order in which two calls are made really do not
matter. If swapping the order of two method calls will not break your application then there is no reason to enforce
that code structure through a unit test. Unfortunately, you cannot have multiple invocations of a method in PHPUnit
without enforcing call order. In Phake these two notions of call order and multiple invocations are kept completely
distinct. Here is the same test written using Phake.

.. code-block:: php

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testPHPUnitMock()
        {
            $mock = Phake::mock('PhakeTest_MockedClass');

            $mock->fooWithArgument('foo');
            $mock->fooWithArgument('bar');

            Phake::verify($mock)->fooWithArgument('foo');
            Phake::verify($mock)->fooWithArgument('bar');
        }
    }

You can switch the calls around in this example as much as you like and the test will still pass. You can mock as many
different invocations of the same method as you need.

If you would like to verify the exact same parameters are used on a method multiple times (or they all match the same
constraints multiple times) then you can use the verification mode parameter of ``Phake::verify()``. The second
parameter to ``Phake::verify()`` allows you to specify how many times you expect that method to be called with matching
parameters. If no value is specified then the default of one is used. The other options are:

* ``Phake::times($n)`` – Where ``$n`` equals the exact number of times you expect the method to be called.
* ``Phake::atLeast($n)`` – Where ``$n`` is the minimum number of times you expect the method to be called.
* ``Phake::atMost($n)`` – Where ``$n`` is the most number of times you would expect the method to be called.
* ``Phake::never()`` - Same as calling ``Phake::times(0)``.

Here is an example of this in action.

.. code-block:: php

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testPHPUnitMock()
        {
            $mock = Phake::mock('PhakeTest_MockedClass');

            $mock->fooWithArgument('foo');
            $mock->fooWithArgument('foo');

            Phake::verify($mock, Phake::times(2))->fooWithArgument('foo');
        }
    }

Verifying Calls Happen in a Particular Order
============================================

Sometimes the desired behavior is that you verify calls happen in a particular order. Say there is a functional reason
for the two variants of ``fooWithArgument()`` to be called in the order of the original test. You can utilize
``Phake::inOrder()`` to ensure the order of your call invocations. ``Phake::inOrder()`` takes one or more arguments and
errors out in the event that one of the verified calls was invoked out of order. The calls don’t have to be in exact
sequential order, there can be other calls in between, it just ensures the specified calls themselves are called in
order relative to each other. Below is an example Phake test that behaves similarly to the PHPUnit test that utilized
``at()``.

.. code-block:: php

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testPHPUnitMock()
        {
            $mock = Phake::mock('PhakeTest_MockedClass');

            $mock->fooWithArgument('foo');
            $mock->fooWithArgument('bar');

            Phake::inOrder(
                Phake::verify($mock)->fooWithArgument('foo'),
                Phake::verify($mock)->fooWithArgument('bar')
            );
        }
    }

Verifying No Interaction with a Mock so Far
===========================================

Occasionally you may want to ensure that no interactions have occurred with a mock object. This can be done
by passing your mock object to ``Phake::verifyNoInteraction($mock)``. This will not prevent further interaction
with your mock, it will simply tell you whether or not any interaction up to that point has happened. You
can pass multiple arguments to this method to verify no interaction with multiple mock objects.

Verifying No Further Interaction with a Mock
============================================

There is a similar method to prevent any future interaction with a mock. This can be done by passing a mock
object to ``Phake::verifyNoFurtherInteraction($mock)``. You can pass multiple arguments to this method to
verify no further interaction occurs with multiple mock objects.

Verifying No Unverified Interaction with a Mock
============================================

By default any unverified calls to a mock are ignored. That is to say, if a call is made to `$mock->foo()` but
`Phake::verify($mock)->foo()` is never used, then no failures are thrown. If you want to be stricter and ensure that
all calls have been verified you can call `Phake::verifyNoOtherInteractions($mock)` at the end of your test. This will
check and make sure that all calls to your mock have been verified by one or more calls to Phake verify. This method
should only be used in those cases where you can clearly say that it is important that your test knows about all calls
on a particular object. One useful case for instance could be in testing a method that returns a filtered array.

.. code-block:: php

    class FilterTest {
        public function testFilteredList()
        {
            $filter = new MyFilter();
            $list = Phake::Mock('MyList');

            $filter->addEvenToList(array(1, 2, 3, 4, 5), $list);

            Phake::verify($list)->push(2);
            Phake::verify($list)->push(4);

            Phake::verifyNoOtherInteractions($list);
        }
    }

Without `Phake::verifyNoOtherInteractions($list)` you would have to add additional verifications that `$list->push()`
was not called for the odd values in the list. This method should be used only when necessary. Using it in every test
is an anti-pattern that will lead to brittle tests.

Verifying Magic Methods
=======================

Most magic methods can be verified using the method name just like you would any other method. The one exception to this
is the ``__call()`` method. This method is overwritten on each mock already to allow for the fluent api that Phake
utilizes. If you want to verify a particular invocation of ``__call()`` you can verify the actual method call by
mocking the method passed in as the first parameter.

Consider the following class.

.. code-block:: php

    class MagicClass
    {
        public function __call($method, $args)
        {
            return '__call';
        }
    }

You could mock an invocation of the `__call()` method through a userspace call to magicCall() with the following code.

.. code-block:: php

    class MagicClassTest extends PHPUnit_Framework_TestCase
    {
        public function testMagicCall()
        {
            $mock = Phake::mock('MagicClass');

            $mock->magicCall();

            Phake::verify($mock)->magicCall();
        }
    }

If for any reason you need to explicitly verify calls to ``__call()`` then you can use ``Phake::verifyCallMethodWith()``.

.. code-block:: php

    class MagicClassTest extends PHPUnit_Framework_TestCase
    {
        public function testMagicCall()
        {
            $mock = Phake::mock('MagicClass');

            $mock->magicCall(42);

            Phake::verifyCallMethodWith('magicCall', array(42))->isCalledOn($mock);
        }
    }
    