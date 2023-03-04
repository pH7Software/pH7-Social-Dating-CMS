<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined\Func as PredefinedFunc;
use PH7\Test\Unit\AssertionHelper;
use PHPUnit\Framework\TestCase;

final class FuncTest extends TestCase
{
    use AssertionHelper;

    /**
     * @dataProvider dateFormatsProvider
     */
    public function testDataFunction(string $sDateFormat): void
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

    public function dateFormatsProvider(): array
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
