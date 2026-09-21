<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class SmsVerificationModel extends Model
{
    public function isPending(int $iProfileId): bool
    {
        $rStmt = Db::getInstance()->prepare('SELECT COUNT(profileId) FROM' . Db::prefix(DbTableName::MEMBER) . 'WHERE profileId = :profileId AND active = :active AND ban = 0');
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':active', RegistrationCore::SMS_ACTIVATION, \PDO::PARAM_INT);
        $rStmt->execute();
        $bPending = (int)$rStmt->fetchColumn() === 1;
        Db::free($rStmt);

        return $bPending;
    }

    /** Activate only a still-pending, unbanned account and save its verified number atomically. */
    public function activate(int $iProfileId, string $sPhoneNumber): bool
    {
        $sSql = 'UPDATE' . Db::prefix(DbTableName::MEMBER) . 'AS m INNER JOIN' . Db::prefix(DbTableName::MEMBER_INFO) . 'AS i ON i.profileId = m.profileId '
            . 'SET m.active = :approved, i.phone = :phone WHERE m.profileId = :profileId AND m.active = :pending AND m.ban = 0';
        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->bindValue(':approved', RegistrationCore::NO_ACTIVATION, \PDO::PARAM_INT);
        $rStmt->bindValue(':phone', $sPhoneNumber, \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', $iProfileId, \PDO::PARAM_INT);
        $rStmt->bindValue(':pending', RegistrationCore::SMS_ACTIVATION, \PDO::PARAM_INT);
        $rStmt->execute();
        $bActivated = $rStmt->rowCount() > 0;
        Db::free($rStmt);

        return $bActivated;
    }
}
