<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Syntax;
use PHPUnit\Framework\TestCase;

abstract class SyntaxTestCase extends TestCase
{
    protected const FIXTURE_PATH = __DIR__ . '/fixtures/';

    abstract protected function getInputDirectory(): string;

    abstract protected function getOutputDirectory(): string;

    abstract protected function getInputTemplateFileExtension();

    abstract protected function getOutputPhpFileExtension(): string;

    protected function assertFile(string $sName, Syntax $oSyntaxEngine): void
    {
        $sTplCode = file_get_contents(static::FIXTURE_PATH . $this->getInputDirectory() . $sName . $this->getInputTemplateFileExtension());
        $sPhpCode = file_get_contents(static::FIXTURE_PATH . $this->getOutputDirectory() . $sName . $this->getOutputPhpFileExtension());
        $oSyntaxEngine->setCode($sTplCode);
        $oSyntaxEngine->setTemplateFile('layout.tpl');
        $oSyntaxEngine->parse();

        $this->assertSame($sPhpCode, $oSyntaxEngine->getParsedCode());
    }
}
