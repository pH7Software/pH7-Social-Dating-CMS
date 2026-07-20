<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

use PH7\MatchmakingCore;
use PHPUnit\Framework\TestCase;
use stdClass;

require_once PH7_PATH_SYS . 'core/classes/MatchmakingCore.php';

final class MatchmakingCoreTest extends TestCase
{
    public function testScoreIsWithinBounds()
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30), 'city' => 'Sydney', 'country' => 'AU']);
        $oPerfectMatch = $this->createProfile([
            'birthDate' => $this->birthDateForAge(30),
            'city' => 'Sydney',
            'country' => 'AU',
            'lastActivity' => date('Y-m-d H:i:s'),
            'avatar' => '1.jpg'
        ]);

        $fScore = MatchmakingCore::score($oProfile, $oPerfectMatch);

        $this->assertGreaterThan(0.95, $fScore);
        $this->assertLessThanOrEqual(1.0, $fScore);
    }

    public function testCloserAgeScoresHigher()
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        $oSameAge = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        $oMuchOlder = $this->createProfile(['birthDate' => $this->birthDateForAge(50)]);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oMuchOlder),
            MatchmakingCore::score($oProfile, $oSameAge)
        );
    }

    public function testSameCityBeatsSameCountry()
    {
        $oProfile = $this->createProfile(['city' => 'Sydney', 'country' => 'AU']);
        $oSameCity = $this->createProfile(['city' => 'Sydney', 'country' => 'AU']);
        $oSameCountryOnly = $this->createProfile(['city' => 'Melbourne', 'country' => 'AU']);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oSameCountryOnly),
            MatchmakingCore::score($oProfile, $oSameCity)
        );
    }

    public function testCityComparisonIsCaseInsensitive()
    {
        $oProfile = $this->createProfile(['city' => 'SYDNEY']);
        $oCandidate = $this->createProfile(['city' => 'sydney']);
        $oOtherCity = $this->createProfile(['city' => 'Perth']);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oOtherCity),
            MatchmakingCore::score($oProfile, $oCandidate)
        );
    }

    public function testRecentlyActiveScoresHigherThanDormant()
    {
        $oProfile = $this->createProfile([]);
        $oActiveToday = $this->createProfile(['lastActivity' => date('Y-m-d H:i:s')]);
        $oDormant = $this->createProfile(['lastActivity' => date('Y-m-d H:i:s', strtotime('-1 year'))]);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oDormant),
            MatchmakingCore::score($oProfile, $oActiveToday)
        );
    }

    public function testProfileWithAvatarScoresHigher()
    {
        $oProfile = $this->createProfile([]);
        $oWithAvatar = $this->createProfile(['avatar' => '1.jpg']);
        $oWithoutAvatar = $this->createProfile([]);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oWithoutAvatar),
            MatchmakingCore::score($oProfile, $oWithAvatar)
        );
    }

    public function testUnapprovedAvatarDoesNotEarnThePhotoBoost()
    {
        $oProfile = $this->createProfile([]);
        $oApproved = $this->createProfile(['avatar' => '1.jpg', 'approvedAvatar' => 1]);
        $oPending = $this->createProfile(['avatar' => '1.jpg', 'approvedAvatar' => 0]);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oPending),
            MatchmakingCore::score($oProfile, $oApproved)
        );
    }

    public function testFutureBirthDateIsTreatedAsUnknownAge()
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        $oFutureBirthDate = $this->createProfile(['birthDate' => date('Y-m-d', strtotime('+5 years'))]);
        // A far-off but same-age candidate, for comparison against the neutral (0.5) unknown-age score
        $oKnownAge = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);

        // The future/corrupt date must not out-score a genuine same-age match via a bogus computed age
        $this->assertGreaterThanOrEqual(
            MatchmakingCore::score($oProfile, $oFutureBirthDate),
            MatchmakingCore::score($oProfile, $oKnownAge)
        );
        // And it must land on the neutral unknown-age contribution, not a spuriously high one
        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oFutureBirthDate),
            MatchmakingCore::score($oProfile, $this->createProfile(['birthDate' => $this->birthDateForAge(30)]))
        );
    }

    public function testSameCityMatchesDespiteWhitespaceAndCaseDifferences()
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30), 'city' => 'New York']);
        $oMessyCity = $this->createProfile(['birthDate' => $this->birthDateForAge(30), 'city' => '  new   YORK ']);
        $oOtherCity = $this->createProfile(['birthDate' => $this->birthDateForAge(30), 'city' => 'Boston']);

        $this->assertGreaterThan(
            MatchmakingCore::score($oProfile, $oOtherCity),
            MatchmakingCore::score($oProfile, $oMessyCity)
        );
    }

    public function testMissingDataDoesNotCrashScoring()
    {
        $oEmpty = new stdClass();

        $fScore = MatchmakingCore::score($oEmpty, $oEmpty);

        $this->assertGreaterThanOrEqual(0.0, $fScore);
        $this->assertLessThanOrEqual(1.0, $fScore);
    }

    public function testRankOrdersBestFirstAndRespectsLimit()
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30), 'city' => 'Sydney', 'country' => 'AU']);
        $oBest = $this->createProfile([
            'birthDate' => $this->birthDateForAge(30),
            'city' => 'Sydney',
            'country' => 'AU',
            'lastActivity' => date('Y-m-d H:i:s'),
            'avatar' => '1.jpg',
            'username' => 'best'
        ]);
        $oWorst = $this->createProfile(['birthDate' => $this->birthDateForAge(55), 'username' => 'worst']);
        $oMiddle = $this->createProfile([
            'birthDate' => $this->birthDateForAge(33),
            'country' => 'AU',
            'city' => 'Brisbane',
            'username' => 'middle'
        ]);

        $aRanked = MatchmakingCore::rank($oProfile, [$oWorst, $oMiddle, $oBest], 2);

        $this->assertCount(2, $aRanked);
        $this->assertSame('best', $aRanked[0]->username);
        $this->assertSame('middle', $aRanked[1]->username);
    }

    public function testBlendedRankFallsBackToContentRankOnColdStart(): void
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        $oNear = $this->createProfile(['profileId' => 1, 'birthDate' => $this->birthDateForAge(30), 'username' => 'near']);
        $oFar = $this->createProfile(['profileId' => 2, 'birthDate' => $this->birthDateForAge(55), 'username' => 'far']);

        $aRanked = MatchmakingCore::rankBlended($oProfile, [$oFar, $oNear], [], 2);

        $this->assertSame('near', $aRanked[0]->username);
        $this->assertSame('far', $aRanked[1]->username);
    }

    public function testStrongBehavioralAffinityOutranksSlightlyBetterContentScore(): void
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        // Content-wise "similar" is a bit better (same age); "liked" is older but carries max affinity
        $oSimilar = $this->createProfile(['profileId' => 1, 'birthDate' => $this->birthDateForAge(30), 'username' => 'similar']);
        $oLiked = $this->createProfile(['profileId' => 2, 'birthDate' => $this->birthDateForAge(36), 'username' => 'liked']);

        $aRanked = MatchmakingCore::rankBlended($oProfile, [$oSimilar, $oLiked], [2 => 5.0], 2);

        $this->assertSame('liked', $aRanked[0]->username);
    }

    public function testBlendedRankIgnoresAffinityOfUnknownProfiles(): void
    {
        $oProfile = $this->createProfile(['birthDate' => $this->birthDateForAge(30)]);
        $oNear = $this->createProfile(['profileId' => 1, 'birthDate' => $this->birthDateForAge(30), 'username' => 'near']);
        $oFar = $this->createProfile(['profileId' => 2, 'birthDate' => $this->birthDateForAge(55), 'username' => 'far']);

        // Affinity data exists but references a profile that isn't among the candidates
        $aRanked = MatchmakingCore::rankBlended($oProfile, [$oFar, $oNear], [999 => 3.0], 2);

        $this->assertSame('near', $aRanked[0]->username);
    }

    private function createProfile(array $aFields): stdClass
    {
        $oProfile = new stdClass();
        foreach ($aFields as $sName => $mValue) {
            $oProfile->$sName = $mValue;
        }

        return $oProfile;
    }

    private function birthDateForAge(int $iAge): string
    {
        return date('Y-m-d', strtotime("-$iAge years -1 month"));
    }
}
