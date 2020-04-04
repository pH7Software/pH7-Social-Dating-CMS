.. _method-parameter-matchers-section:


*************************
Method Parameter Matchers
*************************

The verification and stubbing functionality in Phake both rely heavily on parameter matching to help the system
understand exactly which calls need to be verified or stubbed. Phake provides several options for setting up parameter
matches.

The most common scenario for matching parameters as you use mock objects is matching on equal variables For this reason
the default matcher will ensure that the parameter you pass to the mock method is equal (essentially using the '=='
notation) to the parameter passed to the actual invocation before validating the call or returning the mocked stub. So
going back to the card game demonstration from the introduction. Consider the following interface:

.. code-block:: php

    interface DealerStrategy
    {
        public function deal(CardCollection $deck, PlayerCollection $players);
    }

Here we have a ``deal()`` method that accepts two parameters. If you want to verify that ``deal()`` was called, chances
are very good that you want to verify the the parameters as well. To do this is as simple as passing those parameters
to the ``deal()`` method on the ``Phake::verify($deal)`` object just as you would if you were calling the actual
``deal()`` method itself. Here is a short albeit silly example:

.. code-block:: php

    //I don't have Concrete versions of
    // CardCollection or PlayerCollection yet
    $deck = Phake::mock('CardCollection');
    $players = Phake::mock('PlayerCollection');


    $dealer = Phake::mock('DealerStrategy');

    $dealer->deal($deck, $players);

    Phake::verify($dealer)->deal($deck, $players);

In this example, if I were to have accidentally made the call to ``deal()`` with a property that was set to null as the
first parameter then my test would fail with the following exception::

    Expected DealerStrategy->deal(equal to
    <object:CardCollection>, equal to <object:PlayerCollection>)
    to be called exactly 1 times, actually called 0 times.
    Other Invocations:
      PhakeTest_MockedClass->deal(<null>,
    equal to <object:PlayerCollection>)

Determining the appropriate method to stub works in exactly the same way.

There may be cases when it is necessary to verify or stub parameters based on something slightly more complex then
basic equality. This is what we will talk about next.

Using PHPUnit Matchers
======================
Phake was developed with PHPUnit in mind. It is not dependent on PHPUnit, however if PHPUnit is your testing framework
of choice there is some special integration available. Any constraints made available by the PHPUnit framework will
work seamlessly inside of Phake. Here is an example of how the `PHPUnit constraints <https://phpunit.de/manual/current/en/appendixes.assertions.html#appendixes.assertions.assertThat.tables.constraints>`_ can be used:

.. code-block:: php

    class TestPHPUnitConstraint extends PHPUnit_Framework_TestCase
    {
        public function testDealNumberOfCards()
        {
            $deck = Phake::mock('CardCollection');
            $players = Phake::mock('PlayerCollection');

            $dealer = Phake::mock('DealerStrategy');
            $dealer->deal($deck, $players, 11);

            Phake::verify($dealer)
                ->deal($deck, $players, $this->greaterThan(10));
        }
    }


I have added another parameter to my ``deal()`` method that allows me to specify the number of cards to deal to each
player. In the test above I wanted to verify that the number passed to this parameter was greater than 10.

For a list of the constraints you have available to you through PHPUnit, I recommend reading the PHPUnit's
documentation on assertions and constraints. Any constraint that can be used with ``assertThat()`` in PHPUnit can also
be used in Phake.

Using Hamcrest Matchers
=======================
If you do not use PHPUnit, Phake also supports `Hamcrest matchers <https://github.com/hamcrest/hamcrest-php#this-is-the-php-port-of-hamcrest-matchers>`_. This is in-line with the Phake's design goal of being
usable with any testing framework. Here is a repeat of the PHPUnit example, this time using SimpleTest and Hamcrest
matchers.

.. code-block:: php

    class TestHamcrestMatcher extends UnitTestCase
    {
        public function testDealNumberOfCards()
        {
            $deck = Phake::mock('CardCollection');
            $players = Phake::mock('PlayerCollection');

            $dealer = Phake::mock('DealerStrategy');
            $dealer->deal($deck, $players, 11);

            Phake::verify($dealer)->deal($deck, $players, greaterThan(10));
        }
    }

.. _wildcard-parameters:
Wildcard Parameters
===================
Frequently when stubbing methods, you do not really care about matching parameters. Often times matching every
parameter for a stub can result in overly brittle tests. If you find yourself in this situation you can use Phake's
shorthand stubbing to instruct Phake that a mock should be stubbed on any invocation. You could also use it to verify a
method call regardless of parameters. This is not a very common use case but it is possible.

To specify that a given stub or verification method should match any parameters, you call the method you are stubbing
or mocking as a property of ``Phake::when()`` or ``Phake::verify()``. The code below will mock any invocation of
$obj->foo() regardless of parameters to return bar.

.. code-block:: php

    class FooTest extends PHPUnit_Framework_TestCase
    {
        public function testAddItemsToCart()
        {
            $obj = Phake::mock('MyObject');

            Phake::when($obj)->foo->thenReturn('bar');

            $this->assertEquals('bar', $obj->foo());
            $this->assertEquals('bar', $obj->foo('a parameter'));
            $this->assertEquals('bar', $obj->foo('multiple', 'parameters'));
        }
    }

If you are familiar with ``Phake::anyParameters()`` then you will recognize that the shorthand functionality is really
just short hand of ``Phake::anyParameters()``. You can still use ``Phake::anyParameters()`` but it will likely be
deprecated at some point in the future.

Default and Variable Parameters
-------------------------------
Wildcards can also come in handy when stubbing or verifying methods with default parameters or variable parameters. In
addition to ``Phake::anyParameters()``, ``Phake::ignoreRemaining()`` can be used to instruct Phake to not attempt to
match any further parameters.

A good example of where this could be handy is if you are mocking or verifying a method where the first parameter is
important to stubbing but maybe the remaining parameters aren't. The code below stubs a factory method where the first
parameter sets an item's name, but the remaining parameters are all available as defaults.

.. code-block:: php

    class MyFactory
    {
        public function createItem($name, $color = 'red', $size = 'large')
        {
            //...
        }
    }

    class MyTest extends PHPUnit_Framework_TestCase
    {
        public function testUsingItemFactory()
        {
            $factory = Phake::mock('MyFactory');

            $factory->createItem('Item1', 'blue', 'small');

            //Verification below will succeed
            Phake::verify($factory)->createItem('Item1', Phake::ignoreRemaining());
        }
    }

Parameter Capturing
===================
As you can see there are a variety of methods for verifying that the appropriate parameters are being passed to
methods. However, there may be times when the prebuilt constraints and matchers simply do not fit your needs. Perhaps
there is method that accepts a complex object where only certain components of the object need to be validated.
Parameter capturing will allow you to store the parameter that was used to call your method so that it can be used in
assertions later on.

Consider the following example where I have defined a ``getNumberOfCards()`` method on the ``CardCollection`` interface.

.. code-block:: php

    interface CardCollection
    {
        public function getNumberOfCards();
    }

I want to create new functionality for a my poker dealer strategy that will check to make sure we are playing with a
full deck of 52 cards when the ``deal()`` call is made. It would be rather cumbersome to create a copy of a
``CardCollection`` implementation that I could be sure would match in an equals scenario. Such a test would look
something like this.

Please note, I do not generally advocate this type of design. I prefer dependency injection to instantiation. So
please remember, this is not an example of clean design, simply an example of what you can do with argument capturing.

.. code-block:: php

    class MyPokerGameTest extends PHPUnit_Framework_TestCase
    {
        public function testDealCards()
        {
            $dealer = Phake::mock('MyPokerDealer');
            $players = Phake::mock('PlayerCollection');

            $cardGame = new MyPokerGame($dealer, $players);

            Phake::verify($dealer)->deal(Phake::capture($deck), $players);

            $this->assertEquals(52, $deck->getNumberOfCards());
        }
    }

You can also capture parameters if they meet a certain condition. For instance, if someone mistakenly passed an array
as the first parameter to the ``deal()`` method then PHPUnit would fatal error out. This can be protected against by
using the the ``Phake::capture()->when()`` method. The ``when()`` method accepts the same constraints that
``Phake::verify()`` accepts. Here is how you could leverage that functionality to bulletproof your captures a little
bit.

.. code-block:: php

    class MyBetterPokerGameTest extends PHPUnit_Framework_TestCase
    {
        public function testDealCards()
        {
            $dealer = Phake::mock('MyPokerDealer');
            $players = Phake::mock('PlayerCollection');

            $cardGame = new MyPokerGame($dealer, $players);

            Phake::verify($dealer)->deal(
                Phake::capture($deck)
                    ->when($this->isInstanceOf('CardCollection')),
                $players
            );

            $this->assertEquals(52, $deck->getNumberOfCards());
        }
    }


This could also be done by using PHPUnit's assertions later on with the captured parameter, however this also has a
side effect of better localizing your error. Here is the error you would see if the above test failed.
::

    Exception: Expected MyPokerDealer->deal(<captured parameter>,
    equal to <object:PlayerCollection>) to be called exactly 1
    times, actually called 0 times.
    Other Invocations:
      PhakeTest_MockedClass->deal(<array>,
    <object:PlayerCollection>)

It should be noted that while it is possible to use argument capturing for stubbing with ``Phake::when()`` I would
discourage it. When stubbing a method, you should only be concerned about making sure an expected value is returned.
Argument capturing in no way helps with that goal. In the worst case scenario, you will have some incredibly difficult
test failures to diagnose.

Beginning in Phake 2.1 you can also capture all values for a given parameter for every matching invocation. For
instance imagine if you have a method ``$foo->process($eventManager)`` that should send a series of events.

.. code-block:: php

    class Foo
    {
        // ...
        public function process(Request $request, EventManager $eventManager)
        {
           $eventManager->fire(new PreProcessEvent($request));
           // ... do stuff
           $eventManager->fire(new PostProcessEvent($request, $result));
        }
    }

If you wanted to verify different aspects of the ``$eventManager->fire()`` calls this would have been very difficult
and brittle using standard argument captors. There is now a new method ``Phake::captureAll()`` that can be used to
capture all otherwise matching invocations of method. The variable passed to ``Phake::captureAll()`` will be set to an
array containing all of the values used for that parameter. So with this function the following test can be written.

.. code-block:: php

    class FooTest
    {
        public function testProcess()
        {
            $foo = new Foo();
            $request = Phake::mock('Request');
            $eventManager = Phake::mock('EventManager');

            $foo->process($request, $eventManager);

            Phake::verify($eventManager, Phake::atLeast(1))->fire(Phake::captureAll($events));

            $this->assertInstanceOf('PreProcessEvent', $events[0]);
            $this->assertEquals($request, $events[0]->getRequest());

            $this->assertInstanceOf('PostProcessEvent', $events[1]);
            $this->assertEquals($request, $events[1]->getRequest());
        }
    }

Custom Parameter Matchers
=========================

An alternative to using argument capturing is creating custom matchers. All parameter matchers implement the interface
``Phake_Matchers_IArgumentMatcher``. You can create custom implementations of this interface. This is especially useful
if you find yourself using a similar capturing pattern over and over again. If I were to rewriting the test above using
a customer argument matcher it would look something like this.

.. code-block:: php

    class FiftyTwoCardDeckMatcher implements Phake_Matchers_IArgumentMatcher
    {
        public function matches(&$argument)
        {
            return ($argument instanceof CardCollection
                && $argument->getNumberOfCards() == 52);
        }

        public function __toString()
        {
            return '<object:CardCollection with 52 cards>';
        }
    }

    class MyBestPokerGameTest extends PHPUnit_Framework_TestCase
    {
        public function testDealCards()
        {
            $dealer = Phake::mock('MyPokerDealer');
            $players = Phake::mock('PlayerCollection');

            $cardGame = new MyPokerGame($dealer, $players);

            Phake::verify($dealer)->deal(new FiftyTwoCardDeckMatcher(), $players);
        }
    }
