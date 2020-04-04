.. _method-stubbing-section:

Method Stubbing
===============

The ``Phake::when()`` method is used to stub methods in Phake. As discussed in the introduction,
stubbing allows an object method to be forced to return a particular value given a set of parameters. Similarly to
``Phake::verify()``, ``Phake::when()`` accepts a mock object generated from
``Phake::mock()`` as its first parameter.

Imagine I was in the process of building the next great online shopping cart. The first thing any
good shopping cart allows is to be able to add items. The most important thing I want to know from
the shopping cart is how much money in merchandise is in there. So, I need to make myself a
ShoppingCart class. I also am going to need some class to define my items.
I am more worried about the money right now and because of that I am keenly aware that any item
in a shopping cart is going to have a price. So I will just create an interface to represent those
items called Item. Now take a minute to marvel at the creativity of those
names. Great, now check out the initial definitions for my objects.

.. code-block:: php

    /**
     * An item that is going to make me rich.
     */
    interface Item
    {
        /**
         * @return money
         */
        public function getPrice();
    }

    /**
     * A customer's cart that will contain items that are going to make me rich.
     */
    class ShoppingCart
    {
        private $items = array();

        /**
         * Adds an item to the customer's order
         * @param Item $item
         */
        public function addItem(Item $item)
        {
            $this->items[] = $item;
        }

        /**
         * Returns the current sub total of the customer's order
         * @return money
         */
        public function getSubTotal()
        {
        }
    }

So, I am furiously coding away at this fantastic new ``ShoppingCart`` class when I
realize, I am doing it wrong! You see, a few years ago I went to this conference with a bunch of
other geeky people to talk about how to make quality software. I am supposed to be writing unit
tests. Here I am, a solid thirteen lines (not counting comments) of code into my awe inspiring
new software and I haven't written a single test. I tell myself, "There's no better time to change
than right now!" So I decide to start testing. After looking at the options I decide PHPUnit with
this sweet new mock library called Phake is the way to go.

My first test is going to be for the currently unimplemented ``ShoppingCart::getSubTotal()``
method. I already have a pretty good idea of what this function is going to need to do. It will
need to look at all of the items in the cart, retrieve their price, add it all together and return
the result. So, in my test I know I am going to need a fixture that sets up a shopping cart with
a few items added. Then I am going to need a test that calls ``ShoppingCart::getSubTotal()``
and asserts that it returns a value equal to the price of the items I added to the cart. One catch
though, I don't have any concrete instances of an ``Item``. I wasn't even planning on doing any of
that until tomorrow. I really want to just focus on the ``ShoppingCart`` class.
Never fear, this is why I decided to use Phake. I remember reading about how it will allow me to
quickly create instance of my classes and interfaces that I can set up stubs for so that method
calls return predictable values. This project is all coming together and I am really excited.

.. code-block:: php

    class ShoppingCartTest extends PHPUnit_Framework_TestCase
    {
        private $shoppingCart;

        private $item1;

        private $item2;

        private $item3;

        protected function setUp()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            Phake::when($this->item1)->getPrice()->thenReturn(100);
            Phake::when($this->item2)->getPrice()->thenReturn(200);
            Phake::when($this->item3)->getPrice()->thenReturn(300);

            $this->shoppingCart = new ShoppingCart();
            $this->shoppingCart->addItem($this->item1);
            $this->shoppingCart->addItem($this->item2);
            $this->shoppingCart->addItem($this->item3);
        }

        public function testGetSub()
        {
            $this->assertEquals(600, $this->shoppingCart->getSubTotal());
        }
    }

My test here shows a very basic use of Phake for creating method stubs. I am creating three different mock
implementations of the ``Item`` class. Then for each of those item classes, I am creating
a stub using ``Phake::when()`` that will return 100, 200, and 300 respectively. I know my method
that I am getting ready to implement will need to call those methods in order to calculate the total cost of the
order.

My test is written so now it is time to see how it fails. I run it with phpunit and see the output below::

    $ phpunit ExampleTests/ShoppingCartTest.php
    PHPUnit 3.5.13 by Sebastian Bergmann.

    F

    Time: 0 seconds, Memory: 8.50Mb

    There was 1 failure:

    1) ShoppingCartTest::testGetSub
    Failed asserting that <null> matches expected <integer:600>.

    /home/mikel/Documents/Projects/Phake/tests/ShoppingCartTest.php:69

    FAILURES!
    Tests: 1, Assertions: 1, Failures: 1.

    Generating code coverage report, this may take a moment.


Now that I have a working (and I by working I mean breaking!) test it is time to look at the code necessary to make
the test pass.

.. code-block:: php

    class ShoppingCart
    {
        // I am cutting out the already seen code. If you want to see it again look at the previous examples!

        /**
         * Returns the current sub total of the customer's order
         * @return money
         */
        public function getSubTotal()
        {
            $total = 0;

            foreach ($this->items as $item)
            {
                $total += $item->getPrice();
            }

            return $total;
        }
    }

The code here is pretty simple. I am just iterating over the ``ShoppingCart::$item`` property,
calling the ``Item::getPrice()`` method, and adding them all together. Now when I run phpunit, the tests were successful
and I am getting off to a great start with my shopping cart.
::

    $ phpunit ExampleTests/ShoppingCartTest.php
    PHPUnit 3.5.13 by Sebastian Bergmann.

    .

    Time: 0 seconds, Memory: 8.25Mb

    OK (1 test, 1 assertion)

    Generating code coverage report, this may take a moment.

So, what is Phake doing here? Phake is providing us a predictable implementation of the ``Item::getPrice()``
method that we can use in our test. It helps me to ensure the when my test breaks I know exactly where it is breaking.
I will not have to be worried that a bad implementation of ``Item::getPrice()`` is breaking my tests.

.. _how-phake-when-works:

How Phake::when() Works
-----------------------
Internally Phake is doing quite a bit when this test runs. The three calls to ``Phake::mock()`` are
creating three new classes that in this case each implement the ``Item`` interface. These new classes
each define implementations of any method defined in the ``Item`` interface. If ``Item``
extended another interface, implementations of all of that parent's defined methods would be created as well. Each
method being implemented in these new classes does a few different things. The first thing that it does is record
the fact that the method was called and stores the parameters that were used to call it. The next significant thing
it does is looks at the stub map for that mock object. The stub map is a map that associates answers to method matchers.
An answer is what a mocked object will return when it is called. By default, a call to a mock object returns a static
answer of NULL. We will discuss answers more in :ref:`answers`. A method matcher has two parts. The
first is the method name. The second is an array of arguments. The array of arguments will then contain various constraints
that are applied to each argument to see if a given argument will match. The most common constraint is an equality constraint
that will match loosely along the same lines as the double equals sign in PHP. We will talk about matchers more in
:ref:`method-parameter-matchers-section`.

When each mock object is initially created, its stub map will be empty. This means that any call to a method on a mock object
is going to return a default answer of NULL. If you want your mock object's methods to return something else you must add answers
to the stub map. The ``Phake::when()`` method allows you to map an answer to a method matcher for a given mock object.
The mock object you want to add the mapping to is passed as the first parameter to ``Phake::when()``. The
``Phake::when()`` method will then return a proxy that can be used add answers to your mock object's stub
map. The answers are added by making method calls on the proxy just as you would on the mock object you are proxying. In
the first example above you saw a call to ``Phake::when($this->item1)->getPrice()``.
The ``getPrice()`` call here was telling Phake that I am about to define a new answer that will be returned
any time ``$this->item->getPrice()`` is called in my code. The call to ``$this->item->getPrice()``
returns another object that you can set the answer on using Phake's fluent api. In the example I called
``Phake::when($this->item1)->getPrice()->thenReturn(100)``. The ``thenReturn()`` method will
bind a static answer to a matcher for ``getPrice()`` in the stub map for $this->item1.

Why do Phake stubs return Null by default?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The reasoning behind this is that generally speaking, each method you test should depend on only what it needs to perform the
(hopefully one) responsibility assigned to it. Normally you will have very controlled delegation to other objects. To help with
localization of errors in your test it is assumed that you will always want to mock external dependencies to keep them from
influencing the results of unit tests dedicated to the behavior of other parts of the system. Another reason for this default
behavior is that it provides consistent and predictable behavior regardless of whether you are testing concrete classes, abstract
classes, or interfaces. It should be noted that this default behavior for concrete methods in classes is different then the default
behavior in PHPUnit. In PHPUnit, you have to explicitly indicate that you are mocking a method, otherwise it will call the actual
method code. There are certainly cases where this is useful and this behavior can be achieved in Phake. I will discuss this aspect
of Phake in :ref:`partial-mocks`.

Overwriting Existing Stubs
--------------------------

My shopping cart application is coming right along. I can add items and the total price seems to be accurate. However,
while I was playing around with my new cart I noticed a very strange problem. I was playing around with the idea of
allowing discounts to be applied to a cart as just additional items that would have a negative price. So while I am
playing around with this idea I notice that the math isn't always adding up. If I start with an item that is $100 and
then add a discount that is $81.40 I see that the total price isn't adding up to $18.60. This is definitely problematic
After doing some further research, I realize I made a silly mistake. I am just using simple floats to calculate the
costs. Floats are by nature inaccurate. Once you start using them in mathematical operations they start to show their
inadequacy for precision. In keeping with the test driven method of creating code I need to create a unit test that
shows this flaw.

.. code-block:: php

    class ShoppingCartTest extends PHPUnit_Framework_TestCase
    {
        private $shoppingCart;

        private $item1;

        private $item2;

        private $item3;

        public function setUp()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            Phake::when($this->item1)->getPrice()->thenReturn(100);
            Phake::when($this->item2)->getPrice()->thenReturn(200);
            Phake::when($this->item3)->getPrice()->thenReturn(300);

            $this->shoppingCart = new ShoppingCart();
            $this->shoppingCart->addItem($this->item1);
            $this->shoppingCart->addItem($this->item2);
            $this->shoppingCart->addItem($this->item3);
        }

        public function testGetSub()
        {
            $this->assertEquals(600, $this->shoppingCart->getSubTotal());
        }

        public function testGetSubTotalWithPrecision()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            Phake::when($this->item1)->getPrice()->thenReturn(100);
            Phake::when($this->item2)->getPrice()->thenReturn(-81.4);
            Phake::when($this->item3)->getPrice()->thenReturn(20);

            $this->shoppingCart = new ShoppingCart();
            $this->shoppingCart->addItem($this->item1);
            $this->shoppingCart->addItem($this->item2);
            $this->shoppingCart->addItem($this->item3);

            $this->assertEquals(38.6, $this->shoppingCart->getSubTotal());
        }
    }

You can see that I added another test method that uses actual floats for some of the prices as opposed to round numbers.
Now when I run my test suite I can see the fantastic floating point issue.
::

    $ phpunit ExampleTests/ShoppingCartTest.php
    PHPUnit 3.5.13 by Sebastian Bergmann.

    .F

    Time: 0 seconds, Memory: 10.25Mb

    There was 1 failure:

    1) ShoppingCartTest::testGetSubTotalWithPrecision
    Failed asserting that <double:38.6> matches expected <double:38.6>.

    /home/mikel/Documents/Projects/Phake/tests/ShoppingCartTest.php:95

    FAILURES!
    Tests: 2, Assertions: 2, Failures: 1.

    Generating code coverage report, this may take a moment.

Once you get over the strangeness of 38.6 not equaling 38.6 I want to discuss streamlining test cases with you. You
will notice that the code in ``ShoppingCartTest::testGetSubTotalWithPrecision()`` contains almost
all duplicate code when compared to ``ShoppingCartTest::setUp()``. If I were to continue following
this pattern of doing things I would eventually have tests that are difficult to maintain. Phake allows you to very
easily override stubs. This is very important in helping you to reduce duplication in your tests and leads to tests
that will be easier to maintain. To overwrite a previous stub you simply have to redefine it. I am going to change
``ShoppingCartTest::testGetSubTotalWithPrecision()`` to instead just redefine the ``getPrice()``
stubs.

.. code-block:: php

    class ShoppingCartTest extends PHPUnit_Framework_TestCase
    {
        private $shoppingCart;

        private $item1;

        private $item2;

        private $item3;

        public function setUp()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            Phake::when($this->item1)->getPrice()->thenReturn(100);
            Phake::when($this->item2)->getPrice()->thenReturn(200);
            Phake::when($this->item3)->getPrice()->thenReturn(300);

            $this->shoppingCart = new ShoppingCart();
            $this->shoppingCart->addItem($this->item1);
            $this->shoppingCart->addItem($this->item2);
            $this->shoppingCart->addItem($this->item3);
        }

        public function testGetSub()
        {
            $this->assertEquals(600, $this->shoppingCart->getSubTotal());
        }

        public function testGetSubTotalWithPrecision()
        {
            Phake::when($this->item1)->getPrice()->thenReturn(100);
            Phake::when($this->item2)->getPrice()->thenReturn(-81.4);
            Phake::when($this->item3)->getPrice()->thenReturn(20);

            $this->assertEquals(38.6, $this->shoppingCart->getSubTotal());
        }
    }

If you rerun this test you will get the same results shown in before.
The test itself is much simpler though there is much less unnecessary duplication. The reason this works is because
the stub map I was referring to in :ref:`how-phake-when-works` isn't really a map at all. It is more of
a stack in reality. When a new matcher and answer pair is added to a mock object, it is added to the top of the stack.
Then whenever a stub method is called, the stack is checked from the top down to find the first matcher that matches
the method that was called. So, when I created the additional stubs for the various ``Item::getPrice()``
calls, I was just adding additional matchers to the top of the stack that would always get matched first by virtue
of the parameters all being the same.

Resetting A Mock's Stubs
------------------------
If overriding a stub does not work for your particular case and you would rather start over with all default stubs then
you can use ``Phake::reset()`` and ``Phake::staticReset()``. These will remove all stubs from a mock and also empty
out all recorded calls against a mock. ``Phake::reset()`` will do this for instance methods on the mock and
``Phake::staticReset()`` will do this for all static methods on the mock.

.. code-block:: php
    public function testResettingStubMapper()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        Phake::when($mock)->foo()->thenReturn(42);

        $this->assertEquals(42, $mock->foo());

        Phake::reset($mock);
        //$mock->foo() now returns the default stub which in this case is null
        $this->assertNull($mock->foo());
    }

    public function testResettingCallRecorder()
    {
        $mock = Phake::mock('PhakeTest_MockedClass');
        $mock->foo();

        //Will work as normal
        Phake::verify($mock)->foo();

        Phake::reset($mock);

        //Will now throw an error that foo was not called
        Phake::verify($mock)->foo();
    }

.. _stubbing-multiple-calls:

Stubbing Multiple Calls
-----------------------

Another benefit of the stub mapping in Phake is that it allows you to very easily stub multiple calls to the same
method that use different parameters. In my shopping cart I have decided to add some functionality that will allow
me to easily add multiple products that are a part of a group to the shopping cart. To facilitate this I have decided
to create a new class called ``ItemGroup``. The ``ItemGroup`` object will be
constructed with an array of ``Items``. It will have a method on the class that will add all of
the items in the group to the given cart and then the total price of items in the cart will be returned.

It should be noted that earlier I decided to make a small change to the ``ShoppingCart::addItem()``
method to have it return the total price of items in the cart. I figured that this would be nice api level functionality
to make working with the system a little bit easier. I would like to take advantage of that change with this code.
Here's a stub of the functionality I am considering.

.. code-block:: php

    /**
     * A group of items that can be added to a cart all at the same time
     */
    class ItemGroup
    {
        /**
         * @param array $items an array of Item objects
         */
        public function __construct(array $items)
        {
        }

        /**
         * @param ShoppingCart $cart
         * @return money The new total value of the cart
         */
        public function addItemsToCart(ShoppingCart $cart)
        {
        }
    }

The next test I am going to write now is going to be focusing on this new ``ItemGroup::addItemsToCart()``
method. In my test's ``setUp()`` method I'll create a new instance of ``ItemGroup``
which will require one or more ``Item`` implementations. I'll use mocks for those. Then the actual
test case I am going to start with will be a test to assert that ``ItemGroup::addItemsToCart()``
returns the new shopping cart value. I already know that I am going to need to get this value by looking at the
last return value from calls to ``ShoppingCart::addItem()``. To allow for checking this I will mock
``ShoppingCart`` and create three stubs for ``ShoppingCart::addItem()``. Each
stub will be for a call with a different ``Item``.

.. code-block:: php

    class ItemGroupTest extends PHPUnit_Framework_TestCase
    {
        private $itemGroup;

        private $item1;

        private $item2;

        private $item3;

        public function setUp()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            $this->itemGroup = new ItemGroup(array($this->item1, $this->item2, $this->item3));
        }

        public function testAddItemsToCart()
        {
            $cart = Phake::mock('ShoppingCart');
            Phake::when($cart)->addItem($this->item1)->thenReturn(10);
            Phake::when($cart)->addItem($this->item2)->thenReturn(20);
            Phake::when($cart)->addItem($this->item3)->thenReturn(30);

            $totalCost = $this->itemGroup->addItemsToCart($cart);
            $this->assertEquals(30, $totalCost);
        }
    }

In this example the ``ShoppingCart::addItem()`` method is being stubbed three times. Each time it
is being stubbed with a different parameter being passed to ``addItem()``. This a good example of
how parameters are also checked whenever Phake looks at a mock object's stub map for answers. The default behavior
of argument matching is again a loose equality check. Similar to how you would use the double equals operator in PHP.
The other options for argument matching are discussed further in :ref:`method-parameter-matchers-section`.

Stubbing Consecutive Calls
--------------------------

The previous test was a great example for how you can make multiple stubs for a single method however in reality it
is not the best way for that particular test to be written. What if the ``Item`` objects in an
``ItemGroup`` aren't stored in the order they were passed in? I am needlessly binding my test
to the order in which objects are stored. Phake provides the ability to map multiple answers to the same stub. This is
done simply by chaining the answers together. I could rewrite the test from the previous chapter to utilize this
feature of Phake.

.. code-block:: php

    class ItemGroupTest extends PHPUnit_Framework_TestCase
    {
        private $itemGroup;

        private $item1;

        private $item2;

        private $item3;

        public function setUp()
        {
            $this->item1 = Phake::mock('Item');
            $this->item2 = Phake::mock('Item');
            $this->item3 = Phake::mock('Item');

            $this->itemGroup = new ItemGroup(array($this->item1, $this->item2, $this->item3));
        }

        public function testAddItemsToCart()
        {
            $cart = Phake::mock('ShoppingCart');
            Phake::when($cart)->addItem(Phake::anyParameters())->thenReturn(10)
                ->thenReturn(20)
                ->thenReturn(30);

            $totalCost = $this->itemGroup->addItemsToCart($cart);
            $this->assertEquals(30, $totalCost);
        }
    }

You will notice a few of differences between this example and the example in :ref:`stubbing-multiple-calls`. The first
difference is that there is only one call to ``Phake::when()``. The second difference is that I have chained together three
calls to ``thenReturn()``. The third difference is instead of passing one of my mock Item
objects I have passed the result of the ``Phake::anyParameters()`` method. This is a special argument
matcher in Phake that essentially says match any call to the method regardless of the number of parameters or the
value of those parameters. You can learn more about ``Phake::anyParameters()`` in :ref:`wildcard-parameters`.

So, this single call to ``Phake::when()`` is saying: "Whenever a call to ``$cart->addItem()``
is made, regardless of the parameters, return 10 for the first call, 20 for the second call, and 30 for the third
call." If you are using consecutive call stubbing and you call the method more times than you have answers set, the
last answer will continue to be returned. In this example, if ``$cart->addItem()`` were called a fourth
time, then 30 would be returned again.

Stubbing Reference Parameters
-----------------------------

Occasionally you may run into code that utilizes reference parameters to provide additional output
from a method. This is not an uncommon thing to run into with legacy code. Phake provides a custom
parameter matcher (these are discussed further in :ref:`method-parameter-matchers-section`)
that allows you to set reference parameters. It can be accessed using ``Phake::setReference()``.
The only parameter to this matcher is the value you would like to set the reference parameter
to provided all other parameters match.

.. code-block:: php

    interface IValidator
    {
        /**
         * @parm array $data Data to validate
         * @parm array &$errors contains all validation errors if the data is not valid
         * @return boolean True when the data is valid
         */
        public function validate(array $data, array &$errors);
    }

    class ValidationLogger implements IValidator
    {
        private $validator;
        private $log;

        public function __construct(IValidator $validator, Logger $log)
        {
            $this->validator = $validator;
            $this->log = $log;
        }

        public function validate(array $data, array &$errors)
        {
            if (!$this->validator->validate($data, $errors))
            {
                foreach ($errors as $error)
                {
                    $this->log->info("Validation Error: {$error}");
                }

                return FALSE;
            }

            return TRUE;
        }
    }

    class ValidationLoggerTest extends PHPUnit_Framework_TestCase
    {
        public function testValidate()
        {
            //Mock the dependencies
            $validator = Phake::mock('IValidator');
            $log = Phake::mock('Logger');
            $data = array('data1' => 'value');
            $expectedErrors = array('data1 is not valid');

            //Setup the stubs (Notice the Phake::setReference()
            Phake::when($validator)->validate($data, Phake::setReference($expectedErrors))->thenReturn(FALSE);

            //Instantiate the SUT
            $validationLogger = new ValidationLogger($validator, $log);

            //verify the validation is false and the message is logged
            $errors = array();
            $this->assertFalse($validationLogger->validate($data, $errors));
            Phake::verify($log)->info('Validation Error: data1 is not valid');
        }
    }


In the example above, I am testing a new class I have created called ``ValidationLogger``.
It is a decorator for other implementations of ``IValidator`` that allows adding
logging to any other validator. The ``IValidator::validate()`` method will always
return an array of errors into the second parameter (a reference parameter) provided to the method.
These errors are what my logger is responsible for logging. So in order for my test to work properly,
I will need to be able to set that second parameter as a part of my stubbing call.

In the call to ``Phake::when($validator)->validate()`` I have passed a call to
``Phake::setReference()`` as the second parameter. This is causing the mock
implementation of ``IValidator`` to set ``$errors`` in
``ValidationLogger::validate()`` to the array specified by ``$expectedErrors``.
This allows me to quickly and easily validate that I am actually logging the errors returned back
in the reference parameter.

By default ``Phake::setReference()`` will always return true regardless of the
parameter initially passed in. If you would like to only set a reference parameter when that reference
parameter was passed in as a certain value you can use the ``when()`` modifier.
This takes a single parameter matcher as an argument. Below,
you will see that the test has been modified to call ``when()`` on the result
of `Phake::setReference()``. This modification will cause the reference parameter
to be set only if the $errors parameter passed to ``IValidator::validate()``
is initially passed as an empty array.

.. code-block:: php

    class ValidationLoggerTest extends PHPUnit_Framework_TestCase
    {
        public function testValidate()
        {
            //Mock the dependencies
            $validator = Phake::mock('IValidator');
            $log = Phake::mock('Logger');
            $data = array('data1' => 'value');
            $expectedErrors = array('data1 is not valid');

            //Setup the stubs (Notice the Phake::setReference()
            Phake::when($validator)->validate($data, Phake::setReference($expectedErrors)->when(array())->thenReturn(FALSE);

            //Instantiate the SUT
            $validationLogger = new ValidationLogger($validator, $log);

            //verify the validation is false and the message is logged
            $errors = array();
            $this->assertFalse($validationLogger->validate($data, $errors));
            Phake::verify($log)->info('Validation Error: data1 is not valid');
        }
    }


Please note, when you are using ``Phake::setReference()`` you still must provide
an answer for the stub. If you use this function and your reference parameter is never changed,
that is generally the most common reason.

.. _partial-mocks:

Partial Mocks
-------------

When testing legacy code, if you find that the majority of the methods in the mock are using the ``thenCallParent()``
answer, you may find it easier to just use a partial mock in Phake. Phake partial mocks also allow you to call the
actual constructor of the class being mocked. They are created using ``Phake::partialMock()``. Like ``Phake::mock()``,
the first parameter is the name of the class that you are mocking. However, you can pass additional parameters that
will then be passed as the respective parameters to that class’ constructor. The other notable feature of a partial
mock in Phake is that its default answer is to pass the call through to the parent as if you were using
``thenCallParent()``.

Consider the following class that has a method that simply returns the value passed into the constructor.

.. code-block:: php

    class MyClass
    {
        private $value;

        public __construct($value)
        {
            $this->value = $value;
        }

        public function foo()
        {
            return $this->value;
        }
    }

Using ``Phake::partialMock()`` you can instantiate a mock object that will allow this object to function
as designed while still allowing verification as well as selective stubbing of certain calls.
Below is an example that shows the usage of ``Phake::partialMock()``.

.. code-block:: php

    class MyClassTest extends PHPUnit_Framework_TestCase
    {
        public function testCallingParent()
        {
            $mock = Phake::partialMock('MyClass', 42);

            $this->assertEquals(42, $mock->foo());
        }
    }

Again, partial mocks should not be used when you are testing new code. If you find yourself using them be sure to
inspect your design to make sure that the class you are creating a partial mock for is not doing too much.

Setting Default Stubs
---------------------

You can also change the default stubbing for mocks created with ``Phake::mock()``. This is done by using the second
parameter to ``Phake::mock()`` in conjunction with the ``Phake::ifUnstubbed()`` method. The second parameter to
``Phake::mock()`` is reserved for configuring the behavior of an individual mock. ``Phake::ifUnstubbed()`` allows you
to specify any of the matchers mentioned above as the default answer if any method invocation is not explicitly
stubbed. If this configuration directive is not provided then the method will return NULL by default. An example of
this can be seen below.

.. code-block:: php

    class MyClassTest extends PHPUnit_Framework_TestCase
    {
        public function testDefaultStubs()
        {
            $mock = Phake::mock('MyClass', Phake::ifUnstubbed()->thenReturn(42));

            $this->assertEquals(42, $mock->foo());
        }
    }

Stubbing Magic Methods
----------------------

Most magic methods can be stubbed using the method name just like you would any other method. The one exception to this
is the ``__call()`` method. This method is overwritten on each mock already to allow for the fluent api that Phake
utilizes. If you want to stub a particular invocation of ``__call()`` you can create a stub for the method you are
targetting in the first parameter to ``__call()``.

Consider the following class.

.. code-block:: php

    class MagicClass
    {
        public function __call($method, $args)
        {
            return '__call';
        }
    }

You could stub an invocation of the ``__call()`` method through a userspace call to ``magicCall()`` with the following code.

.. code-block:: php

    class MagicClassTest extends PHPUnit_Framework_TestCase
    {
        public function testMagicCall()
        {
            $mock = Phake::mock('MagicClass');

            Phake::when($mock)->magicCall()->thenReturn(42);

            $this->assertEquals(42, $mock->magicCall());
        }
    }

If for any reason you need to explicitly stub calls to ``__call()`` then you can use ``Phake::whenCallMethodWith()``.
The matchers passed to ``Phake::whenCallMethod()`` will be matched to the method name and array of arguments similar to
what you would expect to be passed to a ``__call()`` method. You can also use Phake::anyParameters() instead.

.. code-block:: php

    class MagicClassTest extends PHPUnit_Framework_TestCase
    {
        public function testMagicCall()
        {
            $mock = Phake::mock('MagicClass');

            Phake::whenCallMethodWith('magicCall', array())->isCalledOn($mock)->thenReturn(42);

            $this->assertEquals(42, $mock->magicCall());
        }
    }
