<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\EmptyCodeException;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Tal as TalSyntax;
use PHPUnit_Framework_TestCase;

class TalTest extends PHPUnit_Framework_TestCase
{
    /** @var TalSyntax */
    private $oTalSyntax;

    protected function setUp()
    {
        $this->oTalSyntax = new TalSyntax;
    }

    public function testParseUnsetCode()
    {
        $this->expectException(EmptyCodeException::class);
        $this->expectExceptionCode(EmptyCodeException::TAL_SYNTAX);

        $this->oTalSyntax->parse();
    }

    public function testParseValidEchoCode()
    {
        $sTalCode = <<<TAL
<ph:print value="Hello World" />
<ph:if test="true">
    <ph:lang value="Bonjour !" />
</ph:if>
TAL;
        $sPhpCode = <<<PHP
<?php echo "Hello World"  ?>
<?php if(true) { ?>
    <?php echo t("Bonjour !" ); ?>
<?php } ?>
PHP;

        $this->oTalSyntax->set($sTalCode);
        $this->oTalSyntax->parse();

        $this->assertSame($sPhpCode, $this->oTalSyntax->get());
    }
}
