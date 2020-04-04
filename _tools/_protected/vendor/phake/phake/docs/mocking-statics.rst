**********************
Mocking Static Methods
**********************

Phake can be used to verify as well as stub polymorphic calls to static methods. It is important to note that you
cannot verify or stub all static calls. In order for Phake to record or stub a method call, it needs to intercept the
call so that it can record it. Consider the following class

.. code-block:: php

    class StaticCaller
    {
        public function callStaticMethod()
        {
            Foo::staticMethod();
        }
    }

You will not be able to stub or verify the call to Foo::staticMethod() because the call was made directly on the class.
This prevents Phake from seeing that the call was made. However, say you have an abstract class that has an abstract
static method.

.. code-block:: php

    abstract class StaticFactory
    {
        protected static function factory()
        {
            // ...
        }

        public static function getInstance()
        {
            return static::factory();
        }
    }

In this case, because the ``static::`` keyword will cause the called class to be determined at runtime, you will be able
to verify and stub calls to StaticFactory::factory(). It is important to note that if self::factory() was called then
stubs and verifications would not work, because again the class is determined at compile time with the self:: keyword.
The key thing to remember with testing statics using Phake is that you can only test statics that leverage Late Static
Binding: http://www.php.net/manual/en/language.oop5.late-static-bindings.php

The key to testing static methods using Phake is that you need to create a "seam" for your static methods. If you are
not familiar with that term, a seam is a location where Phake is able to override and intercept calls to your code.
The typical seem for Phake is a parameter that allows you to pass your object. Typically you would pass a real object,
however during testing you pass in a mock object created by Phake. This is taking advantage of an instance seam.

Thankfully in php now you can do something along the lines of $myVar::myStatic() where if $myVar is a string it
resolves as you would think for a static method. The useful piece though is that if $myVar is an object, it will
resolve that object down to the class name and use that for the static.

So, the general idea here is that you can take code that is in class Foo:

.. code-block:: php

    class Foo
    {
        public function doSomething()
        {
            // ... code that does stuff ...
            Logger::logData();
        }
    }

which does not provide a seam for mocking Logger::logData() and provide that seem by changing it to:

.. code-block:: php

    class Foo
    {
        public $logger = 'Logger';
        public function doSomething()
        {
            // ... code that does stuff ...
            $logger = $this->logger;
            $logger::logData($data);
        }
    }

Now you can mock logData as follows:

.. code-block:: php

    class FooTest
    {
        public function testDoSomething()
        {
            $foo = new Foo();
            $foo->logger = Phake::mock('Logger');
            $foo->doSomething();
            Phake::verifyStatic($foo->logger)->logData(Phake::anyParameters());
        }
    }

Phake has alternative methods to handle interacting with static methods on your mock class. ``Phake::mock()`` is still
used to create the mock class, but the remaining interactions with static methods use more specialized methods. The
table below shows the Phake methods that have a separate counterpart for interacting with static calls.

+-----------------------------------+-----------------------------------------+
| Instance Method                   | Static Method                           |
+===================================+=========================================+
| ``Phake::when()``                 | ``Phake::whenStatic()``                 |
+-----------------------------------+-----------------------------------------+
| ``Phake::verify()``               | ``Phake::verifyStatic()``               |
+-----------------------------------+-----------------------------------------+
| ``Phake::verifyCallMethodWith()`` | ``Phake::verifyStaticCallMethodWith()`` |
+-----------------------------------+-----------------------------------------+
| ``Phake::whenCallMethodWith()``   | ``Phake::whenStaticCallMethodWith()``   |
+-----------------------------------+-----------------------------------------+
| ``Phake::reset()``                | ``Phake::resetStatic()``                |
+-----------------------------------+-----------------------------------------+

If you are using Phake to stub or verify static methods then you should call ``Phake::resetStaticInfo()`` in the
the ``tearDown()`` method. This is necessary to reset the stubs and call recorder for the static calls in the event
that the mock class gets re-used.
