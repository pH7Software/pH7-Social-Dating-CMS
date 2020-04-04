## What's New in 2.1.0

### New Features

**#158 - Allow direct calling of protected methods for mocks**
Allow for calling of private and protected methods on mocks using new Phake::makeVisible() and Phake::makeStaticsVisible() wrappers. This will allow for slightly easier testing of legacy code and abstracts. http://phake.readthedocs.org/en/2.1/mocks.html#calling-private-and-protected-methods-on-mocks

### Changes

**#178 - Renamed thenGetReturnByLambda to thenReturnCallback**
The thenGetReturnByLambda just didn't quite sound right and was difficult to remember so we changed the name. While the original method will still work, a deprecation error will be emitted by the code when thenGetReturnByLambda is called with a message that you should use thenReturnCallback instead. The great news is that no other project in their right mind would ever use that method name, so a search and replace should be pretty reliable.

**#144 - Improve Phake::verify error message**
When a method doesn't match you will now be given a more appropriate diff as to why. This should help make life a little easier when debugging failing tests.

**Enhanced integration with Travis-CI**
We are now testing all the things!

**Integrated with Scrutenizer**
We are also measuring all the things