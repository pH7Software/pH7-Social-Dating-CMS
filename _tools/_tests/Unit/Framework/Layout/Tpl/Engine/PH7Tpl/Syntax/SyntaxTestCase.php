<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Test\Unit\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Syntax;
use PHPUnit_Framework_TestCase;

abstract class SyntaxTestCase extends PHPUnit_Framework_TestCase
{
    const FIXTURE_PATH = __DIR__ . '/fixtures/';

    /**
     * @return string
     */
    abstract protected function getInputDirectory();

    /**
     * @return string
     */
    abstract protected function getOutputDirectory();

    /**
     * @return string
     */
    abstract protected function getInputTemplateFileExtension();

    /**
     * @return string
     */
    abstract protected function getOutputPhpFileExtension();

    /**
     * @param string $sName
     * @param Syntax $oSyntaxEngine
     *
     * @return void
     */
    protected function assertFile($sName, Syntax $oSyntaxEngine)
    {
        $sTplCode = file_get_contents(static::FIXTURE_PATH . $this->getInputDirectory() . $sName . $this->getInputTemplateFileExtension());
        $sPhpCode = file_get_contents(static::FIXTURE_PATH . $this->getOutputDirectory() . $sName . $this->getOutputPhpFileExtension());
        $oSyntaxEngine->setCode($sTplCode);
        $oSyntaxEngine->setTemplateFile('layout.tpl');
        $oSyntaxEngine->parse();

        $this->assertSame($sPhpCode, $oSyntaxEngine->getParsedCode());
    }
}
