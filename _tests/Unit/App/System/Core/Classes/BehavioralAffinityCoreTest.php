<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/BehavioralAffinityCore.php';

use PH7\BehavioralAffinityCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BehavioralAffinityCoreTest extends TestCase
{
    public function testRecommendsProfilesLikedByMembersWithSharedTastes(): void
    {
        /*
         * Member 1 and member 2 both liked profiles 10 and 11.
         * Member 2 also liked profile 12 => 12 should be recommended to member 1.
         */
        $aInteractions = [
            [1, 10], [1, 11],
            [2, 10], [2, 11], [2, 12],
        ];

        $aScores = BehavioralAffinityCore::getAffinityScores($aInteractions, 1);

        $this->assertSame([12], array_keys($aScores));
        $this->assertGreaterThan(0.0, $aScores[12]);
    }

    public function testStrongerTasteOverlapRanksHigher(): void
    {
        /*
         * Member 2 shares two likes with member 1 and also liked profile 20.
         * Member 3 shares only one like and also liked profile 21.
         * => 20 must rank above 21 for member 1.
         */
        $aInteractions = [
            [1, 10], [1, 11],
            [2, 10], [2, 11], [2, 20],
            [3, 10], [3, 21],
        ];

        $aScores = BehavioralAffinityCore::getAffinityScores($aInteractions, 1);

        $this->assertSame([20, 21], array_keys($aScores));
    }

    public function testNeverRecommendsAlreadySeenProfilesNorSelf(): void
    {
        $aInteractions = [
            [1, 10],
            [2, 10], [2, 1], [2, 11],
        ];

        $aScores = BehavioralAffinityCore::getAffinityScores($aInteractions, 1);

        $this->assertArrayNotHasKey(1, $aScores, 'Must never recommend the member to themselves');
        $this->assertArrayNotHasKey(10, $aScores, 'Must never recommend an already-liked profile');
        $this->assertArrayHasKey(11, $aScores);
    }

    public function testHyperactiveMembersDoNotDominate(): void
    {
        /*
         * Member 2 (focused) shares 1 of their 2 likes with member 1 => weight 0.5 on profile 20.
         * Member 3 (hyperactive) shares 1 of their 10 likes => weight 0.1 on profile 30.
         */
        $aInteractions = [[1, 10], [2, 10], [2, 20], [3, 10]];
        for ($i = 31; $i <= 39; $i++) {
            $aInteractions[] = [3, $i];
        }
        // Re-point one of member 3's likes to profile 30 for the comparison
        $aInteractions[4] = [3, 30];

        $aScores = BehavioralAffinityCore::getAffinityScores($aInteractions, 1);

        $this->assertGreaterThan($aScores[30], $aScores[20]);
    }

    public function testResultCountIsCapped(): void
    {
        $aInteractions = [[1, 10], [2, 10]];
        for ($i = 100; $i < 130; $i++) {
            $aInteractions[] = [2, $i];
        }

        $aScores = BehavioralAffinityCore::getAffinityScores($aInteractions, 1, 5);

        $this->assertCount(5, $aScores);
    }

    #[DataProvider('coldStartProvider')]
    public function testColdStartReturnsNoScores(array $aInteractions): void
    {
        $this->assertSame([], BehavioralAffinityCore::getAffinityScores($aInteractions, 1));
    }

    public static function coldStartProvider(): array
    {
        return [
            'no interactions at all' => [[]],
            'member has no own history' => [[[2, 10], [3, 10]]],
            'only malformed rows' => [[[5], [], ['x']]],
            'only self-interactions' => [[[1, 1]]],
        ];
    }
}
