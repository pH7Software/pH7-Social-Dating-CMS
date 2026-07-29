<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Sms Verification / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvider.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/SmsProvidable.php';
require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/ClickatellProvider.php';

use PH7\ClickatellProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class ClickatellProviderTest extends TestCase
{
    public function testIsSuccessResponseReturnsTrueForSuccessfulPayload(): void
    {
        $oProvider = new ClickatellProvider('+61400000000', 'api-token');
        $oMethod = new ReflectionMethod(ClickatellProvider::class, 'isSuccessResponse');

        $this->assertTrue($oMethod->invoke($oProvider, ['messages' => [['error' => false]]]));
    }

    public function testIsSuccessResponseReturnsFalseWhenMessagesKeyIsMissing(): void
    {
        $oProvider = new ClickatellProvider('+61400000000', 'api-token');
        $oMethod = new ReflectionMethod(ClickatellProvider::class, 'isSuccessResponse');

        $this->assertFalse($oMethod->invoke($oProvider, ['result' => 'ok']));
    }

    public function testIsSuccessResponseReturnsFalseWhenErrorKeyIsMissing(): void
    {
        $oProvider = new ClickatellProvider('+61400000000', 'api-token');
        $oMethod = new ReflectionMethod(ClickatellProvider::class, 'isSuccessResponse');

        $this->assertFalse($oMethod->invoke($oProvider, ['messages' => [['id' => 'abc123']]]));
    }
}
