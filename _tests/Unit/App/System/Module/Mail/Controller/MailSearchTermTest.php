<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Mail / Controller
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Mail\Controller;

use PHPUnit\Framework\TestCase;

final class MailSearchTermTest extends TestCase
{
    /**
     * MailModel::search() trims its search term, and trim(null) is deprecated
     * (a TypeError from PHP 9). Listings without a search must pass an empty string.
     */
    public function testMailListingsNeverPassNullAsTheSearchTerm(): void
    {
        $aNullSearches = [];

        foreach (glob(PH7_PATH_SYS_MOD . 'mail/controllers/*.php') as $sFile) {
            $aIgnored = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
            $aTokens = array_values(array_filter(
                token_get_all(file_get_contents($sFile)),
                static fn($mToken): bool => !is_array($mToken) || !in_array($mToken[0], $aIgnored, true)
            ));

            foreach ($aTokens as $iIndex => $mToken) {
                $bSearchCall = is_array($mToken) && $mToken[0] === T_STRING && $mToken[1] === 'search'
                    && is_array($aTokens[$iIndex - 1] ?? null) && $aTokens[$iIndex - 1][0] === T_OBJECT_OPERATOR
                    && ($aTokens[$iIndex + 1] ?? null) === '(';

                $mFirstArgument = $aTokens[$iIndex + 2] ?? null;
                if ($bSearchCall && is_array($mFirstArgument) && strtolower($mFirstArgument[1]) === 'null') {
                    $aNullSearches[] = basename($sFile) . ':' . $mToken[2];
                }
            }
        }

        $this->assertSame([], $aNullSearches);
    }
}
