Introduction to Phake
=====================

**Phake** is a mocking framework for PHP. It allows for the creation of objects that mimic a real
object in a predictable and controlled manner. This allows you to treat external method calls
made by your system under test (SUT) as just another form of input to your SUT and output from
your SUT. This is done by stubbing methods that supply indirect input into your test and by
verifying parameters to methods that receive indirect output from your test.

In true Las Vegas spirit I am implementing a new framework that allows you to easily create
new card games. Most every card game at one point or another needs a dealer. In the example below
I have created a new class called ``CardGame`` that implements the basic functionality for a card game:

.. code-block:: php

    class CardGame
    {
        private $dealerStrategy;
        private $deck;
        private $players;

        public function CardGame(DealerStrategy $dealerStrategy, CardCollection $deck, PlayerCollection $players)
        {
            $this->dealerStrategy = $dealerStrategy;
            $this->deck = $deck;
            $this->players = $players;
        }

        public function dealCards()
        {
            $this->deck->shuffle();
            $this->dealerStrategy->deal($deck, $players);
        }
    }

If I want to create a new test to ensure that ``dealCards()`` works properly, what do I need to test?
Everything I read about testing says that I need to establish known input for my test, and then
test its output. However, in this case, I don’t have any parameters that are passed into ``dealCards()``
nor do I have any return values I can check. I could just run the ``dealCards()`` method and make sure
I don’t get any errors or exceptions, but that proves little more than my method isn’t blowing up
spectacularly. It is apparent that I need to ensure that what I actually assert is that
the ``shuffle()`` and ``deal()`` methods are being called. If
I want to continue testing this using concrete
classes that already exist in my system, I could conjure up one of my implementations of ``DealerStrategy``,
``CardCollection`` and ``PlayerCollection``. All of those
objects are closer to being true value objects
with a testable state. I could feasibly construct instances of those objects, pass them into an
instance of ``CardGame``, call ``dealCards()`` and then assert
the state of those same objects. A test doing
this might look something like:

.. code-block:: php

    class CardGameTest1 extends PHPUnit_Framework_TestCase
    {
        public function testDealCards()
        {
            $dealer = new FiveCardPokerDealer();
            $deck = new StandardDeck();
            $player1 = new Player();
            $player2 = new Player();
            $player3 = new Player();
            $player4 = new Player();
            $players = new PlayerCollection(array($player1, $player2, $player3, $player4));

            $cardGame = new CardGame($dealer, $deck, $players);
            $cardGame->dealCards();

            $this->assertEquals(5, count($player1->getCards()));
            $this->assertEquals(5, count($player2->getCards()));
            $this->assertEquals(5, count($player3->getCards()));
            $this->assertEquals(5, count($player4->getCards()));
        }
    }

This test isn’t all that bad, it’s not difficult to understand and it does make sure that cards
are dealt through making sure that each player has 5 cards. There are at least two significant problems
with this test however. The first problem is that there is not any isolation of the SUT which in
this case is ``dealCards()``. If something is broken in the ``FiveCardPokerDealer``
class, the ``Player`` class,
or the ``PlayerCollection`` class, it will manifest itself here as a broken ``CardGame``
class. Thinking
about how each of these classes might be implemented, one could easily make the argument that this
really tests the ``FiveCardPokerDealer`` class much more than the ``dealCards()`` method.
The second problem
is significantly more problematic. It is perfectly feasible that I could remove the call to ``$this->deck->shuffle()``
in my SUT and the test I have created will still test just fine. In order to solidify my test I
need to introduce logic to ensure that the deck has been shuffled. With the current mindset of using
real objects in my tests I could wind up with incredibly complicated logic. I could feasibly add
an identifier of some sort to ``DealerStrategy::shuffle()`` to mark the deck as shuffled thereby making
it checkable state, however that makes my design more fragile as I would have to ensure that identifier
was set probably on every implementation of ``DealerStrategy::shuffle()``.

This is the type of problem that mock frameworks solve. A mock framework such as Phake can
be used to create implementations of my ``DealerStrategy``, ``CardCollection``, and ``PlayerCollection`` classes.
I can then exercise my SUT. Finally, I can verify that the methods that should be called on these
objects were called correctly. If this test were
re-written to use Phake, it would become:

.. code-block:: php

    class CardGameTest2 extends PHPUnit_Framework_TestCase
    {
        public function testDealCards()
        {
            $dealer = Phake::mock('DealerStrategy');
            $deck = Phake::mock('CardCollection');
            $players = Phake::mock('PlayerCollection');

            $cardGame = new CardGame($dealer, $deck, $players);
            $cardGame->dealCards();

            Phake::verify($deck)->shuffle();
            Phake::verify($dealer)->deal($deck, $players);
        }
    }

There are three benefits of using mock objects that can be seen through this example. The first benefit
is that the brittleness of the fixture is reduced. In our previous example you see that I have to construct
a full object graph based on the dependencies of all of the classes involved. I am fortunate in
the first example that there are only 4 classes involved. In real world problems and especially
long lived, legacy code the object graphs can be much, much larger. When using mock objects you
typically only have to worry about the direct dependencies of your SUT. Specifically, direct dependencies
required to instantiate the dependencies of the class under test, the parameters passed to the method
under test (direct dependencies,) and the values returned by additional method calls within the
method under test (indirect dependencies.)

The second benefit is the test is only testing the SUT. If this test fails due to a change in anything
but the interfaces of the classes involved, the change would have had to been made in either the
constructor of ``CardGame``, or the ``dealCards()`` method itself.
Obviously, if an interface change is
made (such as removing the ``shuffle()``) method, then I would have a scenario
where the changed code is outside of this class. However, provided the removal of that method was
intentional, I will know that this code needs to be addressed as it is depending on a method that no longer exists.

The third benefit is that I have truer verification and assertions of the outcome of exercising
my SUT. In this case for instance, I can be sure that if the call to ``shuffle()`` is removed, this
test will fail. It also does it in a way that keeps the code necessary to assert your final state
simple and concise. This makes my test overall much easier to understand and maintain. There is
still one flaw with this example however. There is nothing here to ensure that ``shuffle()`` is called
before ``deal()`` it is quite possible for someone to mistakenly reverse the order of these two calls.
The Phake framework does have the ability to track call order to make this test even more bullet
proof via the ``Phake::inOrder()`` method. I will go over this in more detail later.

