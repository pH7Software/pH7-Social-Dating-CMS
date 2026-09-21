<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\LostPassword\Models;

use PDO;
use PDOStatement;
use PH7\PasswordResetModel;
use PH7\PasswordResetToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class PasswordResetModelTest extends TestCase
{
    private PasswordResetModel $oModel;

    protected function setUp(): void
    {
        class_alias(ResetDbStub::class, 'PH7\\Framework\\Mvc\\Model\\Engine\\Db');
        require PH7_PATH_SYS_MOD . 'lost-password/inc/class/PasswordResetToken.php';
        require PH7_PATH_SYS_MOD . 'lost-password/models/PasswordResetModel.php';
        $this->oModel = (new ReflectionClass(PasswordResetModel::class))->newInstanceWithoutConstructor();
    }

    #[DataProvider('provideAccountTables')]
    public function testIssuingStoresOnlyAPasswordBoundDigest(string $sTable): void
    {
        $this->queueAccount(['profileId' => 42, 'password' => 'current-hash', 'hashValidation' => 'previous']);
        $this->queueUpdate(1);
        $sToken = $this->oModel->issue('owner@example.test', $sTable);
        self::assertNotNull($sToken);
        self::assertSame(PasswordResetToken::hash($sToken, 'current-hash'), ResetDbStub::$aBindings[1][':hash']);
        self::assertSame('current-hash', ResetDbStub::$aBindings[1][':password']);
        self::assertStringContainsString('UPDATE ph7_' . $sTable . ' SET hashValidation', ResetDbStub::$aSql[1]);
        self::assertSame('owner@example.test', ResetDbStub::$aBindings[0][':email']);
    }

    #[DataProvider('provideAccountTables')]
    public function testResetConsumesTheMatchingTokenAtomically(string $sTable): void
    {
        $sToken = PasswordResetToken::create();
        $sDigest = PasswordResetToken::hash($sToken, 'current-hash');
        $this->queueAccount(['profileId' => 42, 'password' => 'current-hash', 'hashValidation' => $sDigest]);
        $this->queueUpdate(1);
        self::assertSame(42, $this->oModel->reset('owner@example.test', $sToken, 'new<password>&', $sTable));
        self::assertTrue(password_verify('new<password>&', ResetDbStub::$aBindings[1][':newPassword']));
        self::assertSame($sDigest, ResetDbStub::$aBindings[1][':hash']);
        self::assertSame('current-hash', ResetDbStub::$aBindings[1][':password']);
        self::assertMatchesRegularExpression('/^[0-9a-f]{40}$/D', ResetDbStub::$aBindings[1][':newHash']);
        self::assertNotSame($sDigest, ResetDbStub::$aBindings[1][':newHash']);
        self::assertStringContainsString('AND password = :password AND hashValidation = :hash LIMIT 1', ResetDbStub::$aSql[1]);
    }

    public static function provideAccountTables(): array
    {
        return [['members'], ['affiliates'], ['admins']];
    }

    public function testConcurrentResetCannotReportSuccess(): void
    {
        $sToken = PasswordResetToken::create();
        $this->queueAccount(['profileId' => 42, 'password' => 'current-hash', 'hashValidation' => PasswordResetToken::hash($sToken, 'current-hash')]);
        $this->queueUpdate(0);
        self::assertNull($this->oModel->reset('owner@example.test', $sToken, 'new-password', 'members'));
    }

    public function testChangedPasswordInvalidatesTheLinkBeforeAnyWrite(): void
    {
        $sToken = PasswordResetToken::create();
        $aAccount = ['profileId' => 42, 'password' => 'newer-hash', 'hashValidation' => PasswordResetToken::hash($sToken, 'original-hash')];
        $this->queueAccount($aAccount);
        $this->queueAccount($aAccount);
        self::assertFalse($this->oModel->isValid('owner@example.test', $sToken, 'members'));
        self::assertNull($this->oModel->reset('owner@example.test', $sToken, 'new-password', 'members'));
        self::assertCount(2, ResetDbStub::$aSql);
        foreach (ResetDbStub::$aSql as $sSql) {
            self::assertStringStartsWith('SELECT ', $sSql);
        }
    }

    public function testDeletedAccountCannotReceiveALink(): void
    {
        $this->queueAccount(false);
        self::assertNull($this->oModel->issue('deleted@example.test', 'members'));
        self::assertCount(1, ResetDbStub::$aSql);
    }

    public function testExpiredOrLegacyLinkDoesNotQueryTheDatabase(): void
    {
        $sToken = 'r' . sprintf('%07x', intdiv(time() - 3600, 60)) . str_repeat('a', 32);
        self::assertFalse($this->oModel->isValid('owner@example.test', $sToken, 'members'));
        self::assertNull($this->oModel->reset('owner@example.test', str_repeat('a', 40), 'new-password', 'members'));
        self::assertSame([], ResetDbStub::$aSql);
    }

    public function testInvalidTableIsRejectedBeforeSqlConstruction(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->oModel->issue('owner@example.test', 'members WHERE 1=1');
    }

    private function queueAccount(array|false $aAccount): void
    {
        $oStatement = $this->createMock(PDOStatement::class);
        $oStatement->method('fetch')->with(PDO::FETCH_ASSOC)->willReturn($aAccount);
        $this->queueStatement($oStatement);
    }

    private function queueUpdate(int $iAffectedRows): void
    {
        $oStatement = $this->createMock(PDOStatement::class);
        $oStatement->method('rowCount')->willReturn($iAffectedRows);
        $this->queueStatement($oStatement);
    }

    private function queueStatement(PDOStatement $oStatement): void
    {
        $iIndex = count(ResetDbStub::$aStatements);
        $oStatement->method('bindValue')->willReturnCallback(static function (string $sName, mixed $mValue) use ($iIndex): bool {
            ResetDbStub::$aBindings[$iIndex][$sName] = $mValue;

            return true;
        });
        $oStatement->expects(self::once())->method('execute')->willReturn(true);
        ResetDbStub::$aStatements[] = $oStatement;
    }
}

final class ResetDbStub
{
    public static array $aStatements = [];
    public static array $aSql = [];
    public static array $aBindings = [];

    public static function getInstance(): self
    {
        return new self();
    }

    public static function prefix(string $sTable): string
    {
        return ' ph7_' . $sTable . ' ';
    }

    public function prepare(string $sSql): PDOStatement
    {
        $iIndex = count(self::$aSql);
        self::$aSql[] = $sSql;

        return self::$aStatements[$iIndex];
    }

    public static function free(&$rStatement): void
    {
        $rStatement = null;
    }
}
