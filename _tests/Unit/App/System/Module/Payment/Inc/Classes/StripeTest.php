<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Payment / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'payment/inc/class/Api.php';
require_once PH7_PATH_SYS_MOD . 'payment/inc/class/Stripe.php';

use PH7\Stripe;
use PHPUnit\Framework\TestCase;

final class StripeTest extends TestCase
{
    public function testAmount(): void
    {
        $iAmount = Stripe::getAmount('19.99');

        $this->assertSame('1999', $iAmount);
    }
}
