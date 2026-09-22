<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class PluralMessageGrammarTest extends TestCase
{
    /**
     * The CSV import reported "3 users has been successfully added.". The gettext extension
     * looks plural messages up by their singular form, so correcting a plural form keeps
     * existing translations on hosts that have it.
     */
    public function testPluralMessagesUsePluralVerbs(): void
    {
        $aSingularVerbs = [];
        $oFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PH7_PATH_APP));

        foreach ($oFiles as $oFile) {
            if ($oFile->getExtension() !== 'php') {
                continue;
            }

            $sSource = (string)file_get_contents($oFile->getPathname());
            preg_match_all("/\bnt\(\s*'[^']*',\s*'(%n% \w+ (?:has|is|was)\b[^']*)'/", $sSource, $aMatches);
            foreach ($aMatches[1] as $sPluralMessage) {
                $aSingularVerbs[] = str_replace(PH7_PATH_APP, '', $oFile->getPathname()) . ': ' . $sPluralMessage;
            }
        }

        $this->assertSame([], $aSingularVerbs);
    }
}
