*************
Configuration
*************

There are some options you can use to configure and customize Phake. None of these options are required and
Phake can always just be used straight out of the box, however some configuration options are available to
provide more convenient integration with PHPUnit and ability to debug your mock objects.

Setting the Phake Client
========================

While Phake does not have a direct dependency on PHPUnit, there is a PHPUnit specific client that improves
error reporting and allows you to utilize strict mode with PHPUnit. Without using the PHPUnit client, any
failed verifications will result in an errored test. Generally speaking, with PHPUnit, the error result is
reserved for bad tests, not failed tests.

The other issue you would run into when using Phake with PHPUnit without using the PHPUnit Phake client is
that any test runs utilizing the --strict flag will fail when an assertion is not recorded. By default Phake
does not register assertions with PHPUnit. When the PHPUnit client is used however, the assertions are
recorded and --strict mode can be safely used with your tests.

To enable the PHPUnit Phake client, you can register it in your test bootstrap.

.. code-block:: php

    require_once('Phake.php');
    Phake::setClient(Phake::CLIENT_PHPUNIT);

Setting the Mock Class Loader
=============================

When generating mock classes, Phake will load them into memory utilizing the PHP ``eval()`` function. This can
make the code inside of mock classes difficult to debug or diagnose when errors occur in this code. Using
the ``Phake::setMockLoader()`` method you can change this behavior to instead dump the generated class to a
file and then require that file. This will allow for accurate and easily researchable errors when running
tests. This shouldn't typically be required for most users of Phake, however if your are having errors or
working on code for Phake itself it can be incredibly useful.

``Phake::setMockLoader()`` accepts a single parameter of type ``Phake_ClassGenerator_ILoader``. The default
behavior is contained in the ``Phake_ClassGenerator_EvalLoader`` class. If you would instead like to dump the
classes to files you can instead use the ``Phake_ClassGenerator_FileLoader`` class. The constructor accepts a
single parameter containing the directory you would like to dump the classes to. The classes will be stored
in files with the same name as the generated class.

Below is an example of the code required to dump mock classes into the /tmp folder.

.. code-block:: php

    require_once('Phake.php');
    require_once('Phake/ClassGenerator/FileLoader.php');
    Phake::setMockLoader(new Phake_ClassGenerator_FileLoader('/tmp'));
