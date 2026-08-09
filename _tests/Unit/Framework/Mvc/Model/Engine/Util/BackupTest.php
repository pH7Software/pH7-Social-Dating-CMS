<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc / Model / Engine / Util
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine\Util;

use PH7\Framework\Mvc\Model\Engine\Util\Backup;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class BackupTest extends TestCase
{
    public function testInsertSerializationPreservesValuesAndQuotesIdentifiers(): void
    {
        $oMethod = new ReflectionMethod(Backup::class, 'buildInsertStatement');
        $cQuote = static fn(string $sValue): string => "'" . str_replace("'", "''", $sValue) . "'";

        $sStatement = $oMethod->invoke(
            null,
            'Order Details',
            [
                'select' => "O'Reilly",
                'DisplayName' => null,
                'Mixed`Identifier' => 'Active'
            ],
            $cQuote
        );

        $this->assertSame(
            "INSERT INTO `Order Details` (`select`, `DisplayName`, `Mixed``Identifier`) " .
            "VALUES ('O''Reilly', NULL, 'Active');\n",
            $sStatement
        );
    }
}
