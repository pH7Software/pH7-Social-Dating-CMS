<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment;

use PHPUnit\Framework\TestCase;

final class PaymentNotificationMailBoundaryTest extends TestCase
{
    private const CONTROLLER_PATH = PH7_PATH_SYS_MOD . 'payment/controllers/MainController.php';

    public function testMemberDataUsesTheCorrectMailOutputContexts(): void
    {
        $sController = file_get_contents(self::CONTROLLER_PATH);

        $this->assertIsString($sController);
        $this->assertStringContainsString(
            'escapeAttribute((new UserCore())->getProfileLink($sUsername))',
            $sController
        );
        $this->assertStringContainsString("escape(\$sFirstName) . ' (<a href=\"'", $sController);
        $this->assertStringContainsString("escape(\$sUsername) . '</a>)'", $sController);
        $this->assertStringContainsString("preg_replace('/[\\r\\n]+/'", $sController);
        $this->assertStringContainsString("'subject' => t('New Payment Received from %0%', \$sBuyerText)", $sController);
        $this->assertStringNotContainsString("'subject' => t('New Payment Received from %0%', \$sBuyerHtml)", $sController);
    }

    public function testStoredPaymentDetailsAreEscapedAsHtmlText(): void
    {
        $sController = file_get_contents(self::CONTROLLER_PATH);

        $this->assertIsString($sController);
        $this->assertStringContainsString("membership_name = escape(t('Membership name: %0%'", $sController);
        $this->assertStringContainsString("ip = escape(t('Buyer account IP address: %0%'", $sController);
        $this->assertStringContainsString('escapeAttribute(Kernel::PATREON_URL)', $sController);
    }
}
