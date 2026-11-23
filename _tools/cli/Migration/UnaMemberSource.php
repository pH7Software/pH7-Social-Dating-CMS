<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Cli / Migration
 */

declare(strict_types=1);

namespace PH7\Cli\Migration;

final class UnaMemberSource
{
    private const REQUIRED_COLUMNS = [
        'sys_accounts' => ['id', 'profile_id', 'name', 'email', 'email_confirmed', 'active', 'ip'],
        'sys_profiles' => ['id', 'account_id', 'type', 'content_id', 'status'],
        'bx_persons_data' => ['id', 'fullname', 'last_name', 'description', 'gender', 'birthday']
    ];

    private \PDO $oDb;
    private string $sTablePrefix;

    public function __construct(\PDO $oDb, string $sTablePrefix = '')
    {
        if (preg_match('/^[A-Za-z0-9_]*$/D', $sTablePrefix) !== 1) {
            throw new \InvalidArgumentException('The UNA table prefix may contain only letters, numbers, and underscores.');
        }

        $this->oDb = $oDb;
        $this->sTablePrefix = $sTablePrefix;
    }

    public function assertCompatible(): void
    {
        foreach (self::REQUIRED_COLUMNS as $sTable => $aExpectedColumns) {
            $sSourceTable = $this->sTablePrefix . $sTable;
            $rStatement = $this->oDb->prepare(
                'SELECT COLUMN_NAME FROM information_schema.COLUMNS ' .
                'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tableName'
            );
            $rStatement->bindValue(':tableName', $sSourceTable, \PDO::PARAM_STR);
            $rStatement->execute();
            $aColumns = $rStatement->fetchAll(\PDO::FETCH_COLUMN);
            $aMissingColumns = array_values(array_diff($aExpectedColumns, $aColumns));

            if ($aMissingColumns !== []) {
                throw new \RuntimeException(sprintf('UNA 14.0.0 compatibility check failed for %s: missing %s.', $sSourceTable, implode(', ', $aMissingColumns)));
            }
        }
    }

    public function countMembers(int $iAfterId = 0): int
    {
        $rStatement = $this->oDb->prepare(
            'SELECT COUNT(*) ' . $this->buildJoinSql() . ' WHERE a.id > :afterId'
        );
        $rStatement->bindValue(':afterId', $iAfterId, \PDO::PARAM_INT);
        $rStatement->execute();

        return (int)$rStatement->fetchColumn();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fetchBatch(int $iAfterId, int $iBatchSize): array
    {
        if ($iAfterId < 0 || $iBatchSize < 1 || $iBatchSize > 5000) {
            throw new \InvalidArgumentException('Use a non-negative source ID and a batch size between 1 and 5000.');
        }

        $sSql = 'SELECT a.id AS source_id, a.name, a.email, a.email_confirmed, ' .
            'a.active AS account_active, a.ip, p.status AS profile_status, ' .
            'd.fullname, d.last_name, d.description, d.gender, d.birthday ' .
            $this->buildJoinSql() .
            ' WHERE a.id > :afterId ORDER BY a.id ASC LIMIT :batchSize';
        $rStatement = $this->oDb->prepare($sSql);
        $rStatement->bindValue(':afterId', $iAfterId, \PDO::PARAM_INT);
        $rStatement->bindValue(':batchSize', $iBatchSize, \PDO::PARAM_INT);
        $rStatement->execute();

        return $rStatement->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function buildJoinSql(): string
    {
        $sAccounts = $this->quoteIdentifier($this->sTablePrefix . 'sys_accounts');
        $sProfiles = $this->quoteIdentifier($this->sTablePrefix . 'sys_profiles');
        $sPersons = $this->quoteIdentifier($this->sTablePrefix . 'bx_persons_data');

        return sprintf(
            'FROM %s a INNER JOIN %s p ON p.id = a.profile_id AND p.account_id = a.id AND p.type = \'bx_person\' ' .
            'INNER JOIN %s d ON d.id = p.content_id',
            $sAccounts,
            $sProfiles,
            $sPersons
        );
    }

    private function quoteIdentifier(string $sIdentifier): string
    {
        return '`' . $sIdentifier . '`';
    }
}
