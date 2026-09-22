<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / User / Controller
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\User\Controller;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class FriendSearchTermTest extends TestCase
{
    private const SEARCH_TERM_ARGUMENT = 2; // FriendCoreModel::get($iIdProfileId, $iFriendId, $mLooking, ...)

    /**
     * FriendCoreModel::get() trims its search term, and trim(null) is deprecated
     * (a TypeError from PHP 9). Counting mutual friends on a profile passed null,
     * so every profile view would fail. Callers without a search must pass "".
     */
    public function testFriendLookupsNeverPassNullAsTheSearchTerm(): void
    {
        $aNullSearches = [];
        $oFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PH7_PATH_APP));

        foreach ($oFiles as $oFile) {
            if ($oFile->getExtension() !== 'php') {
                continue;
            }

            $aIgnored = [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT];
            $aTokens = array_values(array_filter(
                token_get_all(file_get_contents($oFile->getPathname())),
                static fn($mToken): bool => !is_array($mToken) || !in_array($mToken[0], $aIgnored, true)
            ));

            foreach ($aTokens as $iIndex => $mToken) {
                if ($this->isFriendModelGetCall($aTokens, $iIndex)) {
                    $aArgument = $this->getArgumentTokens($aTokens, $iIndex + 1, self::SEARCH_TERM_ARGUMENT);
                    if (count($aArgument) === 1 && is_array($aArgument[0]) && strtolower($aArgument[0][1]) === 'null') {
                        $aNullSearches[] = str_replace(PH7_PATH_APP, '', $oFile->getPathname()) . ':' . $mToken[2];
                    }
                }
            }
        }

        $this->assertSame([], $aNullSearches);
    }

    /**
     * Matches "(new FriendCoreModel)->get(", "(new FriendModel)->get(" and "->oFriendModel->get(".
     */
    private function isFriendModelGetCall(array $aTokens, int $iIndex): bool
    {
        $mToken = $aTokens[$iIndex];
        if (!is_array($mToken) || $mToken[0] !== T_STRING || $mToken[1] !== 'get'
            || !is_array($aTokens[$iIndex - 1] ?? null) || $aTokens[$iIndex - 1][0] !== T_OBJECT_OPERATOR
            || ($aTokens[$iIndex + 1] ?? null) !== '('
        ) {
            return false;
        }

        $mReceiver = $aTokens[$iIndex - 2] ?? null;
        if ($mReceiver === ')') {
            $mReceiver = $aTokens[$iIndex - 3] ?? null;
            return is_array($mReceiver) && in_array($mReceiver[1], ['FriendCoreModel', 'FriendModel'], true);
        }

        return is_array($mReceiver) && $mReceiver[1] === 'oFriendModel';
    }

    /**
     * Tokens of the zero-based argument at $iPosition, for the call whose "(" is at $iOpen.
     */
    private function getArgumentTokens(array $aTokens, int $iOpen, int $iPosition): array
    {
        $iDepth = 0;
        $iArgument = 0;
        $aArgument = [];

        for ($i = $iOpen; $i < count($aTokens); $i++) {
            $mToken = $aTokens[$i];
            if (in_array($mToken, ['(', '[', '{'], true)) {
                if ($iDepth++ === 0) {
                    continue;
                }
            } elseif (in_array($mToken, [')', ']', '}'], true)) {
                if (--$iDepth === 0) {
                    break;
                }
            } elseif ($mToken === ',' && $iDepth === 1) {
                $iArgument++;
                continue;
            }

            if ($iArgument === $iPosition) {
                $aArgument[] = $mToken;
            }
        }

        return $aArgument;
    }
}
