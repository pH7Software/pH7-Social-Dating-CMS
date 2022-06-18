<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Ajax
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Ajax;

use PH7\Framework\Ajax\Ajax;
use PHPUnit\Framework\TestCase;

final class AjaxTest extends TestCase
{
    public function testJsonSuccessMsg(): void
    {
        $sActualResult = Ajax::jsonMsg(1, 'Yaaay!');
        $this->assertSame('{"status":1,"txt":"Yaaay!"}', $sActualResult);
    }

    public function testJsonFailuresMsg(): void
    {
        $sActualResult = Ajax::jsonMsg(0, 'Noooo! :(');
        $this->assertSame('{"status":0,"txt":"Noooo! :("}', $sActualResult);
    }
}
