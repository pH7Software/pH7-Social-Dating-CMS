<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Models;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\UserCoreModel;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class RegistrationTransactionBehaviorTest extends TestCase
{
    public function testNestedFailurePreservesTheCallersTransactionAndOriginalException(): void
    {
        $oDb = $this->installSqliteConnection();
        $oDb->exec('CREATE TABLE registrations (id INTEGER PRIMARY KEY)');
        $oDb->beginTransaction();

        $oExpectedException = new RuntimeException('original registration failure');

        try {
            $this->runRegistration(
                static function (Db $oDb) use ($oExpectedException): never {
                    $oDb->exec('INSERT INTO registrations (id) VALUES (1)');
                    throw $oExpectedException;
                }
            );
            $this->fail('The original registration exception was not propagated.');
        } catch (RuntimeException $oActualException) {
            $this->assertSame($oExpectedException, $oActualException);
        }

        $this->assertTrue($oDb->inTransaction());
        $this->assertSame(1, $oDb->queryFetchColAssoc('SELECT COUNT(*) FROM registrations'));

        $oDb->rollBack();

        $this->assertSame(0, $oDb->queryFetchColAssoc('SELECT COUNT(*) FROM registrations'));
    }

    public function testOutermostRegistrationCommitsItsOwnTransaction(): void
    {
        $oDb = $this->installSqliteConnection();
        $oDb->exec('CREATE TABLE registrations (id INTEGER PRIMARY KEY)');

        $mResult = $this->runRegistration(
            static function (Db $oDb): string {
                $oDb->exec('INSERT INTO registrations (id) VALUES (1)');

                return 'created';
            }
        );

        $this->assertSame('created', $mResult);
        $this->assertFalse($oDb->inTransaction());
        $this->assertSame(1, $oDb->queryFetchColAssoc('SELECT COUNT(*) FROM registrations'));
    }

    private function installSqliteConnection(): Db
    {
        $oDbReflection = new ReflectionClass(Db::class);
        $oDb = $oDbReflection->newInstanceWithoutConstructor();
        $oPdo = new PDO('sqlite::memory:');
        $oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        (new ReflectionProperty(Db::class, 'oInstance'))->setValue(null, $oDb);
        (new ReflectionProperty(Db::class, 'oDb'))->setValue(null, $oPdo);

        return $oDb;
    }

    private function runRegistration(callable $oRegistration): mixed
    {
        $oModel = (new ReflectionClass(UserCoreModel::class))->newInstanceWithoutConstructor();
        $oMethod = new ReflectionMethod(UserCoreModel::class, 'runRegistrationTransaction');

        return $oMethod->invoke($oModel, $oRegistration);
    }
}
