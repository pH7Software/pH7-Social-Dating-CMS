<?php

/**
 * @title          Payment Model Class
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @version        1.1
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;

class PaymentModel extends UserCoreModel
{
    public const PAYMENT_REJECTED = 0;
    public const PAYMENT_COMPLETED = 1;
    public const PAYMENT_ALREADY_COMPLETED = 2;

    private const PAYPAL_PROVIDER = 'paypal';
    private const PENDING_CHECKOUT_RETENTION_DAYS = 30;

    /**
     * Update a membership group.
     *
     * @param string $sSection
     * @param string $sValue
     * @param int    $iGroupId
     *
     * @return void
     */
    public function updateMembershipGroup($sSection, $sValue, $iGroupId)
    {
        $this->orm->update(DbTableName::MEMBERSHIP, $sSection, $sValue, 'groupId', $iGroupId);
    }

    /**
     * Add a membership group.
     *
     * @param array $aData the parameters for the insertion in database for the new membership
     *
     * @return void
     */
    public function addMembership(array $aData)
    {
        $this->orm->insert(DbTableName::MEMBERSHIP, $aData);
    }

    /**
     * Delete a membership group.
     *
     * @param int $iGroupId
     *
     * @return void
     */
    public function deleteMembership($iGroupId)
    {
        $this->orm->delete(DbTableName::MEMBERSHIP, 'groupId', $iGroupId);
    }

    public function createPayPalCheckout(
        string $sCheckoutReference,
        int $iProfileId,
        int $iMembershipId,
        string $sMembershipAmount,
        string $sExpectedAmount,
        string $sCurrency,
        string $sMerchantAccount,
        bool $bSandbox,
        string $sCreatedAt
    ): bool {
        $this->deleteExpiredPayPalCheckouts();

        $rStmt = Db::getInstance()->prepare(
            'INSERT INTO' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            '(checkout_reference_hash, provider, profile_id, membership_id, membership_amount,
                expected_amount, expected_currency, merchant_account, sandbox, status, created_at)
            VALUES (:checkoutReferenceHash, :provider, :profileId, :membershipId, :membershipAmount,
                :expectedAmount, :expectedCurrency, :merchantAccount, :sandbox, :status, :createdAt)'
        );
        $rStmt->bindValue(':checkoutReferenceHash', hash('sha256', $sCheckoutReference), \PDO::PARAM_STR);
        $rStmt->bindValue(':provider', self::PAYPAL_PROVIDER, \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':membershipId', $iMembershipId, \PDO::PARAM_INT);
        $rStmt->bindValue(':membershipAmount', $sMembershipAmount, \PDO::PARAM_STR);
        $rStmt->bindValue(':expectedAmount', $sExpectedAmount, \PDO::PARAM_STR);
        $rStmt->bindValue(':expectedCurrency', strtoupper($sCurrency), \PDO::PARAM_STR);
        $rStmt->bindValue(':merchantAccount', $sMerchantAccount, \PDO::PARAM_STR);
        $rStmt->bindValue(':sandbox', (int)$bSandbox, \PDO::PARAM_INT);
        $rStmt->bindValue(':status', 'pending', \PDO::PARAM_STR);
        $rStmt->bindValue(':createdAt', $sCreatedAt, \PDO::PARAM_STR);
        $bCreated = $rStmt->execute();
        Db::free($rStmt);

        return $bCreated;
    }

    public function getPayPalCheckout(string $sCheckoutReference): ?\stdClass
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            'WHERE checkout_reference_hash = :checkoutReferenceHash AND provider = :provider LIMIT 1'
        );
        $rStmt->bindValue(':checkoutReferenceHash', hash('sha256', $sCheckoutReference), \PDO::PARAM_STR);
        $rStmt->bindValue(':provider', self::PAYPAL_PROVIDER, \PDO::PARAM_STR);
        $rStmt->execute();
        $mCheckout = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $mCheckout instanceof \stdClass ? $mCheckout : null;
    }

    public function completePayPalCheckout(
        string $sCheckoutReference,
        string $sProviderTransactionId,
        string $sCompletedAt
    ): int {
        $oDb = Db::getInstance();
        $bTransactionStarted = false;

        try {
            $oDb->beginTransaction();
            $bTransactionStarted = true;

            $oCheckout = $this->getLockedPayPalCheckout($sCheckoutReference);
            if ($oCheckout === null) {
                $oDb->rollBack();

                return self::PAYMENT_REJECTED;
            }

            if ($oCheckout->status === 'completed') {
                $bSameTransaction = is_string($oCheckout->provider_transaction_id)
                    && hash_equals($oCheckout->provider_transaction_id, $sProviderTransactionId);
                $oDb->commit();

                return $bSameTransaction
                    ? self::PAYMENT_ALREADY_COMPLETED
                    : self::PAYMENT_REJECTED;
            }

            if ($oCheckout->status !== 'pending' || $this->isProviderTransactionRecorded($sProviderTransactionId)) {
                $oDb->rollBack();

                return self::PAYMENT_REJECTED;
            }

            if (!$this->doesMemberExistForUpdate((int)$oCheckout->profile_id)) {
                $oDb->rollBack();

                return self::PAYMENT_REJECTED;
            }

            $this->updateMemberFromCheckout($oCheckout, $sCompletedAt);
            $this->markCheckoutCompleted(
                (int)$oCheckout->payment_transaction_id,
                $sProviderTransactionId,
                $sCompletedAt
            );

            $oDb->commit();

            return self::PAYMENT_COMPLETED;
        } catch (\Throwable $oException) {
            if ($bTransactionStarted) {
                try {
                    $oDb->rollBack();
                } catch (\Throwable) {
                    // Preserve the original database exception.
                }
            }

            throw $oException;
        }
    }

    private function deleteExpiredPayPalCheckouts(): void
    {
        $rStmt = Db::getInstance()->prepare(
            'DELETE FROM' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            'WHERE provider = :provider AND status = :status
                AND created_at < NOW() - INTERVAL :retentionDays DAY'
        );
        $rStmt->bindValue(':provider', self::PAYPAL_PROVIDER, \PDO::PARAM_STR);
        $rStmt->bindValue(':status', 'pending', \PDO::PARAM_STR);
        $rStmt->bindValue(':retentionDays', self::PENDING_CHECKOUT_RETENTION_DAYS, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    private function getLockedPayPalCheckout(string $sCheckoutReference): ?\stdClass
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT * FROM' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            'WHERE checkout_reference_hash = :checkoutReferenceHash AND provider = :provider LIMIT 1 FOR UPDATE'
        );
        $rStmt->bindValue(':checkoutReferenceHash', hash('sha256', $sCheckoutReference), \PDO::PARAM_STR);
        $rStmt->bindValue(':provider', self::PAYPAL_PROVIDER, \PDO::PARAM_STR);
        $rStmt->execute();
        $mCheckout = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return $mCheckout instanceof \stdClass ? $mCheckout : null;
    }

    private function isProviderTransactionRecorded(string $sProviderTransactionId): bool
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT payment_transaction_id FROM' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            'WHERE provider = :provider AND provider_transaction_id = :providerTransactionId LIMIT 1 FOR UPDATE'
        );
        $rStmt->bindValue(':provider', self::PAYPAL_PROVIDER, \PDO::PARAM_STR);
        $rStmt->bindValue(':providerTransactionId', $sProviderTransactionId, \PDO::PARAM_STR);
        $rStmt->execute();
        $bRecorded = $rStmt->fetchColumn() !== false;
        Db::free($rStmt);

        return $bRecorded;
    }

    private function doesMemberExistForUpdate(int $iProfileId): bool
    {
        $rStmt = Db::getInstance()->prepare(
            'SELECT profileId FROM' . Db::prefix(DbTableName::MEMBER) .
            'WHERE profileId = :profileId LIMIT 1 FOR UPDATE'
        );
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->execute();
        $bExists = $rStmt->fetchColumn() !== false;
        Db::free($rStmt);

        return $bExists;
    }

    private function updateMemberFromCheckout(\stdClass $oCheckout, string $sCompletedAt): void
    {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::MEMBER) .
            'SET groupId = :membershipId, membershipDate = :completedAt WHERE profileId = :profileId LIMIT 1'
        );
        $rStmt->bindValue(':membershipId', (int)$oCheckout->membership_id, \PDO::PARAM_INT);
        $rStmt->bindValue(':completedAt', $sCompletedAt, \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', (int)$oCheckout->profile_id, \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);
    }

    private function markCheckoutCompleted(
        int $iPaymentTransactionId,
        string $sProviderTransactionId,
        string $sCompletedAt
    ): void {
        $rStmt = Db::getInstance()->prepare(
            'UPDATE' . Db::prefix(DbTableName::PAYMENT_TRANSACTION) .
            'SET provider_transaction_id = :providerTransactionId, status = :status, completed_at = :completedAt
            WHERE payment_transaction_id = :paymentTransactionId AND status = :pendingStatus LIMIT 1'
        );
        $rStmt->bindValue(':providerTransactionId', $sProviderTransactionId, \PDO::PARAM_STR);
        $rStmt->bindValue(':status', 'completed', \PDO::PARAM_STR);
        $rStmt->bindValue(':completedAt', $sCompletedAt, \PDO::PARAM_STR);
        $rStmt->bindValue(':paymentTransactionId', $iPaymentTransactionId, \PDO::PARAM_INT);
        $rStmt->bindValue(':pendingStatus', 'pending', \PDO::PARAM_STR);
        $rStmt->execute();
        if ($rStmt->rowCount() !== 1) {
            Db::free($rStmt);
            throw new \RuntimeException('The payment checkout could not be completed atomically.');
        }
        Db::free($rStmt);
    }
}
