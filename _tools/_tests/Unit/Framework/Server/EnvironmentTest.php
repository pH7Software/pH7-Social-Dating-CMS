<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Server
 */

namespace PH7\Test\Unit\Framework\Server;

use PH7\Framework\Server\Environment;
use PHPUnit_Framework_TestCase;

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
    public function testWrongEnvFile()
    {
        $this->assertSame('production.env', Environment::getFileName('wrong_env_name'));
    }

    public function testCorrectProdEnvName()
    {
        $this->assertSame('production.env', Environment::getFileName('production'));
    }

    public function testCorrectDevEnvName()
    {
        $this->assertSame('development.env', Environment::getFileName('development'));
    }
}
