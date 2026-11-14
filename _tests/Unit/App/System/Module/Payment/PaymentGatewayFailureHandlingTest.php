<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment;

use PHPUnit\Framework\TestCase;

final class PaymentGatewayFailureHandlingTest extends TestCase
{
    public function testControllerNeverDisplaysRawGatewayExceptions(): void
    {
        $sController = $this->readRepositoryFile(
            'payment/controllers/MainController.php'
        );

        $this->assertStringContainsString("error_log(sprintf('Stripe checkout %s: %s'", $sController);
        $this->assertStringContainsString("\$this->setPaymentGatewayFailureMessage('Stripe')", $sController);
        $this->assertStringNotContainsString(
            '$this->design->setMessage($this->str->escape($oException->getMessage()',
            $sController
        );
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(PH7_PATH_SYS_MOD . $sPath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
