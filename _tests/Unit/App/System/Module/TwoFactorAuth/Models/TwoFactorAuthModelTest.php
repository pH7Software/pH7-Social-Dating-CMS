<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\TwoFactorAuth\Models;

use PDO;
use PDOStatement;
use PH7\TwoFactorAuthModel;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class TwoFactorAuthModelTest extends TestCase
{
    public function testAuthenticationReadBypassesProfileCache(): void
    {
        class_alias(AuthProfileDbStub::class, 'PH7\\Framework\\Mvc\\Model\\Engine\\Db');
        require PH7_PATH_SYS_MOD . 'two-factor-auth/models/TwoFactorAuthModel.php';
        $oModel = (new ReflectionClass(TwoFactorAuthModel::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty($oModel, 'sTable'))->setValue($oModel, 'members');
        $oRow = (object)['profileId' => 42, 'ban' => 1];
        $oStatement = $this->createMock(PDOStatement::class);
        $oStatement->expects(self::exactly(2))->method('bindValue')->with(':profileId', 42, PDO::PARAM_INT)->willReturn(true);
        $oStatement->expects(self::exactly(2))->method('execute')->willReturn(true);
        $oStatement->expects(self::exactly(2))->method('fetch')->with(PDO::FETCH_OBJ)->willReturnOnConsecutiveCalls($oRow, false);
        AuthProfileDbStub::$oStatement = $oStatement;
        self::assertSame($oRow, $oModel->getAuthProfile(42));
        self::assertFalse($oModel->getAuthProfile(42));
        self::assertSame(2, AuthProfileDbStub::$iQueries);
        self::assertSame('SELECT * FROM ph7_members WHERE profileId = :profileId LIMIT 1', AuthProfileDbStub::$sSql);
    }
}

final class AuthProfileDbStub
{
    public static PDOStatement $oStatement;
    public static int $iQueries = 0;
    public static string $sSql;

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
        ++self::$iQueries;
        self::$sSql = $sSql;

        return self::$oStatement;
    }

    public static function free(&$rStatement): void
    {
        $rStatement = null;
    }
}
