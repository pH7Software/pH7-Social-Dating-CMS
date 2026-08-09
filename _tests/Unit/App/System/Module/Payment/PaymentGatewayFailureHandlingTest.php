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
    private const REPOSITORY_ROOT = __DIR__ . '/../../../../../..';

    public function testControllerNeverDisplaysRawGatewayExceptions(): void
    {
        $sController = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/controllers/MainController.php'
        );

        $this->assertStringContainsString("error_log(sprintf('Stripe checkout %s: %s'", $sController);
        $this->assertStringContainsString("error_log(sprintf('Braintree checkout failed: %s'", $sController);
        $this->assertStringContainsString("\$this->setPaymentGatewayFailureMessage('Stripe')", $sController);
        $this->assertStringContainsString("\$this->setPaymentGatewayFailureMessage('Braintree')", $sController);
        $this->assertStringNotContainsString(
            '$this->design->setMessage($this->str->escape($oException->getMessage()',
            $sController
        );
    }

    public function testBraintreeSaleFailureIsCaughtAfterInitialization(): void
    {
        $sController = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/controllers/MainController.php'
        );
        $iHandler = strpos($sController, 'private function braintreeHandler()');
        $iTry = strpos($sController, 'try {', (int)$iHandler);
        $iInit = strpos($sController, 'Braintree::init(', (int)$iHandler);
        $iSale = strpos($sController, 'Braintree::sale(', (int)$iHandler);
        $iCatch = strpos($sController, 'catch (\\Throwable $oException)', (int)$iSale);

        $this->assertNotFalse($iHandler);
        $this->assertNotFalse($iTry);
        $this->assertNotFalse($iInit);
        $this->assertNotFalse($iSale);
        $this->assertNotFalse($iCatch);
        $this->assertTrue($iTry < $iInit && $iInit < $iSale && $iSale < $iCatch);
    }

    public function testBraintreeTokenFailureLeavesAnActionableCheckoutPage(): void
    {
        $sDesign = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/inc/class/design/PaymentDesign.php'
        );
        $iTry = strpos($sDesign, 'try {', strpos($sDesign, 'public function buttonBraintree'));
        $iToken = strpos($sDesign, 'Braintree::generateClientToken()', (int)$iTry);
        $iCatch = strpos($sDesign, 'catch (\\Throwable $oException)', (int)$iToken);
        $iFixedMessage = strpos($sDesign, 'Braintree checkout is temporarily unavailable.', (int)$iCatch);

        $this->assertNotFalse($iTry);
        $this->assertNotFalse($iToken);
        $this->assertNotFalse($iCatch);
        $this->assertNotFalse($iFixedMessage);
        $this->assertTrue($iTry < $iToken && $iToken < $iCatch && $iCatch < $iFixedMessage);
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(self::REPOSITORY_ROOT . '/' . $sPath);

        $this->assertIsString($sContents);

        return $sContents;
    }
}
