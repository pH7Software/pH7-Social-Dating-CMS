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
use PH7\Framework\Security\Security;

class PasswordResetModel extends Model
{
    public function issue(string $sEmail, string $sTable): ?string
    {
        $aAccount = $this->getAccount($sEmail, $sTable);
        if (!$aAccount) {
            return null;
        }

        $sToken = PasswordResetToken::create();
        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET hashValidation = :hash WHERE profileId = :profileId AND password = :password LIMIT 1');
        $rStmt->bindValue(':hash', PasswordResetToken::hash($sToken, $aAccount['password']), \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', (int)$aAccount['profileId'], \PDO::PARAM_INT);
        $rStmt->bindValue(':password', $aAccount['password'], \PDO::PARAM_STR);
        $rStmt->execute();
        $bIssued = $rStmt->rowCount() === 1;
        Db::free($rStmt);

        return $bIssued ? $sToken : null;
    }

    public function isValid(string $sEmail, string $sToken, string $sTable): bool
    {
        return PasswordResetToken::isCurrent($sToken)
            && $this->matches($this->getAccount($sEmail, $sTable), $sToken);
    }

    /** Return the profile ID only when the password and token are updated together. */
    public function reset(string $sEmail, string $sToken, string $sPassword, string $sTable): ?int
    {
        if (!PasswordResetToken::isCurrent($sToken)) {
            return null;
        }

        $aAccount = $this->getAccount($sEmail, $sTable);
        if (!$this->matches($aAccount, $sToken)) {
            return null;
        }

        $rStmt = Db::getInstance()->prepare('UPDATE' . Db::prefix($sTable) . 'SET password = :newPassword, hashValidation = :newHash WHERE profileId = :profileId AND password = :password AND hashValidation = :hash LIMIT 1');
        $rStmt->bindValue(':newPassword', Security::hashPwd($sPassword), \PDO::PARAM_STR);
        $rStmt->bindValue(':newHash', bin2hex(random_bytes(20)), \PDO::PARAM_STR);
        $rStmt->bindValue(':profileId', (int)$aAccount['profileId'], \PDO::PARAM_INT);
        $rStmt->bindValue(':password', $aAccount['password'], \PDO::PARAM_STR);
        $rStmt->bindValue(':hash', $aAccount['hashValidation'], \PDO::PARAM_STR);
        $rStmt->execute();
        $bReset = $rStmt->rowCount() === 1;
        Db::free($rStmt);

        return $bReset ? (int)$aAccount['profileId'] : null;
    }

    private function getAccount(string $sEmail, string $sTable): array|false
    {
        if (!in_array($sTable, [DbTableName::MEMBER, DbTableName::AFFILIATE, DbTableName::ADMIN], true)) {
            throw new \InvalidArgumentException('Invalid password reset account type.');
        }

        $rStmt = Db::getInstance()->prepare('SELECT profileId, password, hashValidation FROM' . Db::prefix($sTable) . 'WHERE email = :email LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->execute();
        $aAccount = $rStmt->fetch(\PDO::FETCH_ASSOC);
        Db::free($rStmt);

        return $aAccount;
    }

    private function matches(array|false $aAccount, string $sToken): bool
    {
        return $aAccount !== false
            && hash_equals($aAccount['hashValidation'], PasswordResetToken::hash($sToken, $aAccount['password']));
    }
}
