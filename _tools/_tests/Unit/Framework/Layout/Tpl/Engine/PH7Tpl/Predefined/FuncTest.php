<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined\Func as PredefinedFunc;
use PHPUnit_Framework_TestCase;

class FuncTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sDateFormat
     *
     * @dataProvider dateFormatsProvider
     */
    public function testDataFunction($sDateFormat)
    {
        $oPredefinedFunc = new PredefinedFunc('<ph:date value="' . $sDateFormat . '" />');
        $this->assertAttributeSame(
            '<ph:date value="' . $sDateFormat . '" />',
            'sCode',
            $oPredefinedFunc
        );
        $this->assertSame(
            '<?php echo date(\'' . $sDateFormat . '\')?>',
            $oPredefinedFunc->assign()->get()
        );
    }

    /**
     * @return array
     */
    public function dateFormatsProvider()
    {
        return [
            ['Y/m/d'],
            ['Y'],
            ['F j, Y, g:i a'],
            ['\i\t \i\s \t\h\e jS \d\a\y'],
            ['h-i-s, j-m-y, it is w Day'],
            ['m.d.y']
        ];
    }
}
