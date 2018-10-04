<h1>Coding Conventions for pH7CMS</h1>
<p>When working with pH7CMS's code, developing extensions and modifications in the code, these coding conventions are highly recommended and will make your code easier to read/understand for other devs.</p>

<h2>Class, Interface and Trait Names</h2>
<p>UpperCamelCase (<a href="http://en.wikipedia.org/wiki/StudlyCaps">StudlyCaps</a>) and alphanumeric only.</p>

<h3 id="interface-names">Interface Naming</h3>
<p>
    When you are naming your interface, you should use "-ble" suffix as much as possible (in short, use an adjective for naming it).<br />
    Examples: <cite>Controllable</cite>, <cite>Hashable</cite>, <cite>Configurable</cite>, <cite>Serializable</cite>, <cite>Readable</cite>.
</p>

<h3 id="method-constant-names">Method and Constant Names</h3>
<p>
    Method Name: <a href="http://en.wikipedia.org/wiki/CamelCase">camelCase</a> and alphanumeric only.<br />
    Constant Name: ALL_CAPS and alphanumeric only with the underscores to separate words.
</p>

<p class="italic underline">Example:</p>

<pre>
<code class="php">
class MyClass
{

    const MY_CONSTANT = 'abcd';

    /**
     * @param string $sVal
     * @return string Returns If the $sVal parameter is "abcd", returns "abcd", otherwise "zyxw".
     */
    public function myMethod($sVal)
    {
        if ($sVal == 'abcd')
            return 'abcd';
        else
            return 'zyxw';
    }

}
</code>
</pre>

<h2 id="database-table-names">Database Table Names</h2>
<p>
    all_lowercase and alphabetical only with the underscores to separate each words.<br />
    Table names have to be prefixed with "ph7_" which will be replaced by the user prefix chosen during the installation.
</p>

<h4>In pH7Framework</h4>
<p>The classes should end with ".class.php" extension, traits should end with ".trait.php" extension and interfaces must end with ".interface.php"</p>

<h2 id="variable-names">Variable Names</h2>
<p>The variables must be in camelCase and alphanumeric only.</p>
<p>Since PHP is not a typed language, the data found in the variables are fuzzy, so we defined a strict convention for naming variables.<br />
The first letter of the variable must define the type of this: Here is the list of available types:</p>
<p class="italic underline">Data type prefixes:</p>

<pre>
<cite>
a = Array
i = Integer
f = Float, Double
b = Boolean
c = 1 Character
s = String
by = Byte
r = Resource
o = Object
m = Mixed
</cite>
</pre>

<p>Following the first letter every word used in the Variable Name must begin with a capital letter.</p>

<p class="italic underline">Example:</p>

<pre>
<code class="php">
touch('isSunday.txt'); // Creating an empty file
$sFile = realpath('isSunday.txt');

$iDate = date('D');
$bStatus = ($iDate == 'Sun') ? true : false;
$sValue = ($bStatus) ? 'Good Sunday' : 'We are not Sunday';

$rFile = fopen($sFile, 'w');
fwrite($rFile, $sValue);
fclose($rFile);

readfile($sFile);
</code>
</pre>

<p>We use very infrequently (or in a different way) the PEAR coding standards which requires the names of the members (methods and attributes of a class) of a private class to precede it with an underscore (_).<br />
So to distinguish between private and protected members (methods and attributes) of a class.<br />
But you can still follow this convention if you want ;-).<br />
By cons never put members of a class in public (if you do, it means that you do not know enough object-oriented programming to create a module or a code from us).<br />
Also, we rarely respect the "standard" which requires a line must not exceed 80 characters because we believe this standard and obsolete nowadays screens are larger and have a code too long can become very annoying.</p>

<h2 id="function-global-variable-array-names">Function, Global Variable and Array Names</h2>
Function: lowercase and each word must be separated by underscore.

<p class="italic underline">Example:</p>

<pre>
<code class="php">
function my_function() {}
</code>
</pre>

<p>Global variables (Session, Cookie, <abbr title="\PH7\Framework\Registry\Registry class">Registry</abbr>/Global variables, ...): lowercase and each word must be separated by underscore.</p>

<p class="italic underline">Example:</p>

<pre><code class="php">$GLOBALS['my_values'];</code></pre>

<p>Arrays: lowercase and each word must be separated by underscore.</p>
<p>Arrays should be declared with the shortened syntax "[]"</p>
<p class="italic underline">Example:</p>

<pre>
<code class="php">
$aValues = [
   'my_key' => 'Value',
   'my_key2' => 'Value 2'
];
</code>
</pre>

<p>
    PS: We also respect the PSR-0 and PSR-1 coding standards and we try to respect the PSR-2 coding standards but we do not always do that because some things in this new standards<br />
    are not easily followed and we do not find this especially well, but you should still try to respect this standard and the PEAR standard for your modules and pieces of code.
</p>
