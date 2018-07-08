<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly as CurlySyntax;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;
use PHPUnit_Framework_TestCase;

class CurlyTest extends PHPUnit_Framework_TestCase
{
    /** @var CurlySyntax */
    private $oCurlySyntax;

    protected function setUp()
    {
        $this->oCurlySyntax = new CurlySyntax;
    }

    public function testParseUnsetCode()
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::CURLY_SYNTAX);

        $this->oCurlySyntax->parse();
    }

    public function testParseValidEchoCode()
    {
        $sCurlyCode = <<<CURLY
{% 'Hello World' %}
{if true}
    {lang 'Bonjour'}
{/if}
CURLY;
        $sPhpCode = <<<PHP
<?php echo  'Hello World' ;?>
<?php if(true) { ?>
    <?php echo t('Bonjour'); ?>
<?php } ?>
PHP;

        $this->oCurlySyntax->set($sCurlyCode);
        $this->oCurlySyntax->parse();

        $this->assertSame($sPhpCode, $this->oCurlySyntax->get());
    }
}
