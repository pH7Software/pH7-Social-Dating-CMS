<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc / Model / Engine
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine;

use PH7\Framework\Mvc\Model\Engine\Db;
use PHPUnit\Framework\TestCase;

final class DbTest extends TestCase
{
    public function testRequiredSqlVersionMatchesInstallerRequirement(): void
    {
        $this->assertSame('5.5.3', Db::REQUIRED_SQL_VERSION);
    }
}
