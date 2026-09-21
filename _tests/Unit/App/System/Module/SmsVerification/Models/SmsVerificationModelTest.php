<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Models;

use PDO;
use PDOStatement;
use PH7\RegistrationCore;
use PH7\SmsVerificationModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SmsVerificationModelTest extends TestCase
{
    private SmsVerificationModel $oModel;

    protected function setUp(): void
    {
        class_alias(SmsModelDbStub::class, 'PH7\\Framework\\Mvc\\Model\\Engine\\Db');
        require PH7_PATH_SYS_MOD . 'sms-verification/models/SmsVerificationModel.php';
        $this->oModel = (new ReflectionClass(SmsVerificationModel::class))->newInstanceWithoutConstructor();
    }

    #[DataProvider('provideAffectedRows')]
    public function testActivationUsesOneConditionalUpdate(int $iAffectedRows): void
    {
        $oStatement = $this->createMock(PDOStatement::class);
        $aBindings = [];
        $oStatement->expects(self::exactly(4))->method('bindValue')->willReturnCallback(
            static function (string $sName, mixed $mValue, int $iType) use (&$aBindings): bool {
                $aBindings[$sName] = [$mValue, $iType];

                return true;
            }
        );
        $oStatement->expects(self::once())->method('execute')->willReturn(true);
        $oStatement->method('rowCount')->willReturn($iAffectedRows);
        SmsModelDbStub::$oStatement = $oStatement;
        self::assertSame($iAffectedRows > 0, $this->oModel->activate(42, '+12025550123'));
        self::assertSame([
            ':approved' => [RegistrationCore::NO_ACTIVATION, PDO::PARAM_INT],
            ':phone' => ['+12025550123', PDO::PARAM_STR],
            ':profileId' => [42, PDO::PARAM_INT],
            ':pending' => [RegistrationCore::SMS_ACTIVATION, PDO::PARAM_INT]
        ], $aBindings);
        self::assertStringContainsString('INNER JOIN ph7_members_info AS i ON i.profileId = m.profileId', SmsModelDbStub::$sSql);
        self::assertStringContainsString('m.profileId = :profileId AND m.active = :pending AND m.ban = 0', SmsModelDbStub::$sSql);
    }

    public static function provideAffectedRows(): array
    {
        return [[0], [1], [2]];
    }

    public function testPendingCheckReadsTheCurrentStatusDirectly(): void
    {
        $oStatement = $this->createMock(PDOStatement::class);
        $oStatement->expects(self::exactly(4))->method('bindValue')->willReturn(true);
        $oStatement->expects(self::exactly(2))->method('execute')->willReturn(true);
        $oStatement->method('fetchColumn')->willReturnOnConsecutiveCalls(1, 0);
        SmsModelDbStub::$oStatement = $oStatement;
        self::assertTrue($this->oModel->isPending(42));
        self::assertFalse($this->oModel->isPending(42));
        self::assertSame('SELECT COUNT(profileId) FROM ph7_members WHERE profileId = :profileId AND active = :active AND ban = 0', SmsModelDbStub::$sSql);
    }
}

final class SmsModelDbStub
{
    public static PDOStatement $oStatement;
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
        self::$sSql = $sSql;

        return self::$oStatement;
    }

    public static function free(&$rStatement): void
    {
        $rStatement = null;
    }
}
