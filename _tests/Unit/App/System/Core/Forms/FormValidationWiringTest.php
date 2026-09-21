<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class FormValidationWiringTest extends TestCase
{
    public function testApplicationHandlersValidateTheirOwnForm(): void
    {
        $iCalls = 0;
        $oFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PH7_PATH_PROTECTED . 'app'));
        foreach ($oFiles as $oFile) {
            if ($oFile->getExtension() !== 'php') {
                continue;
            }

            $sSource = file_get_contents($oFile->getPathname());
            preg_match_all('/Form::isValid\(([^\n]+)\)/', $sSource, $aCalls);
            foreach ($aCalls[1] as $sCall) {
                ++$iCalls;
                self::assertMatchesRegularExpression("/^'form_[a-z0-9_]+'\\)/", $sCall, $oFile->getPathname());
                preg_match("/^'(form_[a-z0-9_]+)'/", $sCall, $aId);
                self::assertStringContainsString("new \\PFBC\\Form('" . $aId[1] . "'", $sSource, $oFile->getPathname());
            }
        }

        self::assertGreaterThanOrEqual(79, $iCalls);
    }
}
