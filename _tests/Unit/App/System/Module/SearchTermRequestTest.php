<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class SearchTermRequestTest extends TestCase
{
    /**
     * The "looking" search term is passed to trim() by the model search methods. Read
     * untyped, "?looking[]=x" arrived as an array and crashed nine search pages with a
     * TypeError, so every read must ask for a string.
     */
    public function testSearchTermIsAlwaysReadAsAString(): void
    {
        $aUntypedReads = [];
        $oFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PH7_PATH_APP));

        foreach ($oFiles as $oFile) {
            if ($oFile->getExtension() !== 'php') {
                continue;
            }

            $sSource = file_get_contents($oFile->getPathname());
            $iReads = preg_match_all("/->(?:get|post)\\('looking'\\s*(,\\s*[^)]*)?\\)/", $sSource, $aMatches);
            for ($i = 0; $i < $iReads; $i++) {
                if (strpos($aMatches[1][$i], 'Type::STRING') === false) {
                    $aUntypedReads[] = str_replace(PH7_PATH_APP, '', $oFile->getPathname()) . ': ' . $aMatches[0][$i];
                }
            }
        }

        $this->assertSame([], $aUntypedReads);
    }
}
