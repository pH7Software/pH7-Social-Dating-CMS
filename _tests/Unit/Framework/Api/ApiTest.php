<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\Api;
use PHPUnit\Framework\TestCase;

final class ApiTest extends TestCase
{
    use Api;

    public function testSetWithWrongDataType(): void
    {
        $this->assertFalse($this->set('wrong type'));
    }

    public function testSetWithValidData(): void
    {
        $aData = json_decode('{"status":1,"msg":"Hello World!"}', true);

        $this->assertSame(['status' => 1, 'msg' => 'Hello World!'], $aData);
    }
}
