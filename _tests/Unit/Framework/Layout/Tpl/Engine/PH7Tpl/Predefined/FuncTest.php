<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined\Func as PredefinedFunc;
use PHPUnit_Framework_TestCase;

class FuncTest extends PHPUnit_Framework_TestCase
{
    public function testDataFunction()
    {
        $oPredefinedFunc = new PredefinedFunc('<ph:date value="Y/m/d" />');
        $this->assertAttributeSame(
            '<ph:date value="Y/m/d" />',
            'sCode',
            $oPredefinedFunc
        );
        $this->assertSame(
            '<?php echo date(\'Y/m/d\')?>',
            $oPredefinedFunc->assign()->get()
        );
    }
}
