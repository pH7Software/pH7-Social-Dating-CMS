<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / App / System / Module / Payment
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Payment;

use PHPUnit\Framework\TestCase;

final class PayPalCheckoutWiringTest extends TestCase
{
    private const REPOSITORY_ROOT = __DIR__ . '/../../../../../..';

    public function testCheckoutUsesPersistentNotificationAndResultEndpoints(): void
    {
        $sDesign = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/inc/class/design/PaymentDesign.php'
        );
        $sTemplate = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/views/base/tpl/main/pay.tpl'
        );

        self::assertStringContainsString(
            "->param('notify_url', Uri::get('payment', 'main', 'notify', 'paypal'))",
            $sDesign
        );
        self::assertStringContainsString("Uri::get('payment', 'main', 'result', 'paypal')", $sDesign);
        self::assertStringContainsString('buttonPayPal($membership, $paypal_checkout_reference)', $sTemplate);
        self::assertStringNotContainsString('buttonPayPal($membership, $checkout_token)', $sTemplate);
    }

    public function testOnlyThePayPalNotificationActionBypassesMemberAuthentication(): void
    {
        $sPermission = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/config/Permission.php'
        );

        $sPublicEndpointPattern = <<<'REGEX'
~\$bPublicPayPalEndpoint\s*=\s*\$this->registry->controller\s*===\s*'MainController'.*?\$this->registry->action\s*===\s*'notify';~s
REGEX;

        self::assertMatchesRegularExpression($sPublicEndpointPattern, $sPermission);
        self::assertStringContainsString('&& !$bPublicPayPalEndpoint', $sPermission);
        self::assertStringNotContainsString("registry->action === 'result'", $sPermission);
    }

    public function testControllerUsesThePersistentIdempotentPayPalFlow(): void
    {
        $sController = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/controllers/MainController.php'
        );

        self::assertStringContainsString('createPayPalCheckout(', $sController);
        self::assertStringContainsString('getPayPalCheckout(', $sController);
        self::assertStringContainsString('isValidPayPalNotification(', $sController);
        self::assertStringContainsString('completePayPalCheckout(', $sController);
        self::assertStringNotContainsString('private function paypalHandler', $sController);
        self::assertStringNotContainsString('case self::PAYPAL_GATEWAY_NAME:', $sController);
    }

    public function testResultVerifiesOwnershipBeforeSynchronizingTheSession(): void
    {
        $sController = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/controllers/MainController.php'
        );
        $iResultStart = strpos($sController, 'public function result(): void');
        $iResultEnd = strpos($sController, 'public function info()', (int)$iResultStart);

        self::assertNotFalse($iResultStart);
        self::assertNotFalse($iResultEnd);

        $sResult = substr($sController, (int)$iResultStart, (int)$iResultEnd - (int)$iResultStart);
        $sOwnershipCheck = '(int)$oCheckout->profile_id === $this->iProfileId';
        $sSessionUpdate = '$this->updateUserGroupId((int)$oCheckout->membership_id);';
        $iOwnershipCheck = strpos($sResult, $sOwnershipCheck);
        $iSessionUpdate = strpos($sResult, $sSessionUpdate);

        self::assertStringContainsString('$oCheckout->status === \'completed\'', $sResult);
        self::assertNotFalse($iOwnershipCheck);
        self::assertNotFalse($iSessionUpdate);
        self::assertTrue((int)$iOwnershipCheck < (int)$iSessionUpdate);
    }

    public function testPaymentCompletionIsTransactionalAndLocksTheCheckout(): void
    {
        $sModel = $this->readRepositoryFile(
            '_protected/app/system/modules/payment/models/PaymentModel.php'
        );

        self::assertStringContainsString('beginTransaction()', $sModel);
        self::assertStringContainsString('LIMIT 1 FOR UPDATE', $sModel);
        self::assertStringContainsString('isProviderTransactionRecorded(', $sModel);
        self::assertStringContainsString('$oCheckout->status === \'completed\'', $sModel);
        self::assertStringContainsString("WHERE payment_transaction_id = :paymentTransactionId AND status = :pendingStatus", $sModel);
        self::assertStringContainsString('commit()', $sModel);
        self::assertStringContainsString('rollBack()', $sModel);
    }

    private function readRepositoryFile(string $sPath): string
    {
        $sContents = file_get_contents(self::REPOSITORY_ROOT . '/' . $sPath);

        self::assertIsString($sContents);

        return $sContents;
    }
}
