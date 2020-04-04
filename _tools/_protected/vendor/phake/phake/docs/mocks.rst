Creating Mocks
==============

The ``Phake::mock()`` method is how you create new test doubles in Phake. You pass in the class name of what you would
like to mock.

.. code-block:: php

    $mock = Phake::mock('ClassToMock');

The ``$mock`` variable is now an instance of a generated class that inherits from ``ClassToMock`` with hooks that allow
you to force functions to return known values. By default, all methods on a mock object will return null. This behavior
can be overridden on a per method and even per parameter basis. This will be covered in depth in
:ref:`method-stubbing-section`.

The mock  will also record all calls made to this class so that you can later verify that specific methods were called
with the proper parameters. This will be covered in depth in :ref:`method-verification-section`.

In addition to classes you can also mock interfaces directly. This is done in much the same way as a class name, you
simply pass the interface name as the first parameter to ``Phake::mock()``.

.. code-block:: php

    $mock = Phake::mock('InterfaceToMock');

You can also pass an array of interface names to ``Phake::mock()`` that also contains up to 1 class name. This allows
for easier mocking of a dependency that is required to implement multiple interfaces.

.. code-block:: php

    $mock = Phake::mock(array('Interface1', 'Interface2'));

Partial Mocks
-------------

When testing legacy code, you may find that a better default behavior for the methods is to actually call the original
method. This can be accomplished by stubbing each of the methods to return ``thenCallParent()``. You can learn more
about this in :ref:`then-call-parent`.

While this is certainly possible, you may find it easier to just use a partial mock in Phake. Phake partial mocks also
allow you to call the actual constructor of the class being mocked. They are created using ``Phake::partialMock()``.
Like ``Phake::mock()``, the first parameter is the name of the class that you are mocking. However, you can pass
additional parameters that will then be passed as the respective parameters to that class’ constructor. The other
notable feature of a partial mock in Phake is that its default answer is to pass the call through to the parent as if
you were using ``thenCallParent()``.

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

Calling Private and Protected Methods on Mocks
----------------------------------------------
Beginning in Phake 2.1 it is possible to invoke protected and private methods on your mocks using Phake. When you mock
a class, the mocked version will retain the same visibility on each of its functions as you would have had on your
original class. However, using ``Phake::makeVisible()`` and ``Phake::makeStaticsVisible()`` you can allow direct
invocation of instance methods and static methods accordingly. Both of these methods accept a mock object as its only
parameter and returns a proxy class that you can invoke the methods on. Method calls on these proxies will still
return whatever value was previously stubbed for that method call. So if you intend on the original method being called
and you aren't using :ref:`partial-mocks`, then you can just enable :ref:`calling-the-parent` for that method call using
the ``thenCallParent()`` answer. This is all discussed in greater depth in :ref:`method-stubbing` and :ref:`answers`.

.. code-block:: php

    class MyClass
    {
        private function foo()
        {
        }

        private static function bar()
        {
        }
    }

Given the class above, you can invoke both private methods with the code below.

.. code-block:: php

    $mock = Phake::mock('MyClass');

    Phake::makeVisible($mock)->foo();

    Phake::makeStaticVisible($mock)->bar();

As you can see above when using the static variant you still call the method as though it were an instance method. The
other thing to take note of is that there is no modification done on $mock itself. If you use ``Phake::makeVisible()``
you will only be able to make those private and protected calls off of the return of that method itself.

The best use case for this feature of Phake is if you have private or protected calls that are nested deep inside of
public methods. Generally speaking you would always just test from your class's public interface. However these large
legacy classes often require a significant amount of setup within fixtures to allow for calling those private and
protected methods. If you are only intending on refactoring the private and protected method then using
``Phake::makeVisible()`` removes the need for these complex fixtures.

Consider this really poor object oriented code. The cleanRowContent() function does some basic text processing such as
stripping html tags, cleaning up links, etc. It turns out that the original version of this method is written in a very
unperformant manner and I have been tasked with rewriting it.

.. code-block:: php

    class MyReallyTerribleOldClass
    {
        public function __construct(Database $db)
        {
            //...
        }

        public function doWayTooMuch($data)
        {
            $result = $this->db->query($this->getQueryForData($data))

            $rows = array();
            while ($row = $this->db->fetch($result))
            {
                $rows[] = $this->cleanRowContent($row);
            }

            return $rows;
        }

        private function cleanRowContent($row)
        {
            //...
        }

        private function getQueryForData($data)
        {
            //...
        }
    }

If I was about to make changes to cleanRowContent and wanted to make sure I didn't break previous functionality, in order to
do so with the traditional fixture I would have to write a test similar to the following:

.. code-block:: php

    class Test extends PHPUnit_Framework_TestCase
    {
        public function testProcessRow()
        {
            $dbRow = array('id' => '1', 'content' => 'Text to be processed with <b>tags stripped</b>');
            $expectedValue = array(array('id' => 1', 'content' => 'Text to be processed with tags stripped');

            $db = Phake::mock('Database');
            $result = Phake::mock('DatabaseResult');
            $oldClass = new MyReallyTerribleOldClass($db);

            Phake::when($db)->query->thenReturn($result);

            Phake::when($db)->fetch->thenReturn($dbRow)->thenReturn(null);

            $data = $oldClass->doWayTooMuch(array());

            $this->assertEquals($expectedValue, $data);
        }
    }

Using test helpers or PHPUnit data providers I could reuse this test to make sure I fully cover the various logic paths
and use cases for the cleanRowContent(). However this test is doing alot of work to just set up this scenario. Whenever
your test is hitting code not relevant to your test in increases the test's fragility. Here is how you could test the
same code using ``Phake::makeVisible()``.

.. code-block:: php

    class Test extends PHPUnit_Framework_TestCase
    {
        public function testProcessRow()
        {
            $dbRow = array('id' => '1', 'content' => 'Text to be processed with <b>tags stripped</b>');
            $expectedValue = array('id' => 1', 'content' => 'Text to be processed with tags stripped');

            $oldClass = new Phake::partialMock('MyReallyTerribleOldClass');

            $data = Phake::makeVisible($oldClass)->cleanRowContent($dbRow);
            $this->assertEquals($expectedValue, $dbRow);
        }
    }

As you can see the test is significantly simpler. One final note, if you find yourself using this strategy on newly
written code, it could be a code smell indicitive of a class or public method doing too much. It is very reasonable
to argue that in my example, the ``cleanRowContent()`` method should be a class in and of itself or possibly a method
on a string manipulation type of class that my class then calls out to. This is a better design and also a much easier
to test design.