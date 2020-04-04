Getting Started
===============

Phake depends on PHP 5.3.3 or greater. It has no dependency on PHPUnit and should be usable with
any version of PHPUnit so long as the PHP version is 5.3.3 or greater.

Composer Install
----------------
Phake can be installed via `Composer <https://github.com/composer/composer>`_. You will typically want to install Phake
as a development requirement. To do so you can add the following to your ``composer.json`` file:

.. code-block:: js

    {
        // ..
        "require-dev": {
            "phake/phake": "@stable"
        }
        // ..
    }

Once this is added to ``composer.json`` you can run ``composer update phake/phake``

Install from Source
-------------------
You can also clone a copy of Phake from the `Phake GitHub repository <https://github.com/mlively/Phake>`_.
Every attempt is made to keep the master branch stable and this should be usable for those that
immediately need features before they get released or in the event that you enjoy the bleeding edge.
Always remember, until something goes into a rc state, there is always a chance that the functionality
may change. However as an early adopter that uses GitHub, you can have a chance to mold the software
as it is built.

Support
-------

If you think you have found a bug or an issue with Phake, please feel free to open up an issue on the
`Phake Issue Tracker <https://github.com/mlively/Phake/issues>`_


