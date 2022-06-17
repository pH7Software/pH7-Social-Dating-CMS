<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Server
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Server;

use PH7\Framework\Server\Environment;
use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase
{
    public function testWrongEnvFile(): void
    {
        $this->assertSame('production.env', Environment::getFileName('wrong_env_name'));
    }

    public function testCorrectProdEnvName(): void
    {
        $this->assertSame('production.env', Environment::getFileName('production'));
    }

    public function testCorrectDevEnvName(): void
    {
        $this->assertSame('development.env', Environment::getFileName('development'));
    }
}
