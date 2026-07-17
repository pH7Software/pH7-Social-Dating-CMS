<?php

/**
 * Local compatibility scoring — ranks candidate profiles against a reference
 * profile using only data already stored in the members tables. Everything is
 * computed on the server; no external API is involved.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

final class MatchmakingCore
{
    /** Score weights (sum to 1.0). Age proximity dominates, then location, freshness, photo. */
    public const WEIGHT_AGE = 0.35;
    public const WEIGHT_GEO = 0.25;
    public const WEIGHT_ACTIVITY = 0.25;
    public const WEIGHT_AVATAR = 0.15;

    /** Age difference (in years) at which the age affinity has dropped to ~60% */
    public const AGE_TOLERANCE_YEARS = 5;

    /** Days of inactivity at which the activity score has dropped to ~37% */
    public const ACTIVITY_HALF_LIFE_DAYS = 30;

    public const SAME_CITY_SCORE = 1.0;
    public const SAME_COUNTRY_SCORE = 0.4;

    /**
     * Rank candidate profiles by compatibility with the given profile, best first.
     *
     * @param \stdClass   $oProfile    the reference profile (needs birthDate; city/country when available)
     * @param \stdClass[] $aCandidates rows as returned by UserCoreModel::search()
     *
     * @return \stdClass[] the candidates sorted by descending compatibility, cut to $iLimit
     */
    public static function rank(\stdClass $oProfile, array $aCandidates, int $iLimit): array
    {
        usort($aCandidates, static function (\stdClass $oA, \stdClass $oB) use ($oProfile): int {
            return self::score($oProfile, $oB) <=> self::score($oProfile, $oA);
        });

        return array_slice($aCandidates, 0, $iLimit);
    }

    /**
     * Compatibility score between two profiles, in the 0..1 range.
     */
    public static function score(\stdClass $oProfile, \stdClass $oCandidate): float
    {
        return self::WEIGHT_AGE * self::getAgeAffinity($oProfile, $oCandidate) +
            self::WEIGHT_GEO * self::getGeoAffinity($oProfile, $oCandidate) +
            self::WEIGHT_ACTIVITY * self::getActivityScore($oCandidate) +
            self::WEIGHT_AVATAR * self::getAvatarScore($oCandidate);
    }

    /**
     * Gaussian falloff on the age difference, so a 2-year gap barely costs
     * anything while a 15-year gap scores near zero (no hard cutoff).
     */
    private static function getAgeAffinity(\stdClass $oProfile, \stdClass $oCandidate): float
    {
        $iProfileAge = self::getAge($oProfile);
        $iCandidateAge = self::getAge($oCandidate);

        if ($iProfileAge === null || $iCandidateAge === null) {
            return 0.5; // Unknown ages shouldn't zero the whole score
        }

        $iDiff = abs($iProfileAge - $iCandidateAge);

        return exp(-($iDiff ** 2) / (2 * self::AGE_TOLERANCE_YEARS ** 2));
    }

    private static function getGeoAffinity(\stdClass $oProfile, \stdClass $oCandidate): float
    {
        $sProfileCity = self::normalize($oProfile->city ?? '');
        $sCandidateCity = self::normalize($oCandidate->city ?? '');
        if ($sProfileCity !== '' && $sProfileCity === $sCandidateCity) {
            return self::SAME_CITY_SCORE;
        }

        $sProfileCountry = self::normalize($oProfile->country ?? '');
        $sCandidateCountry = self::normalize($oCandidate->country ?? '');
        if ($sProfileCountry !== '' && $sProfileCountry === $sCandidateCountry) {
            return self::SAME_COUNTRY_SCORE;
        }

        return 0.0;
    }

    /**
     * Exponential decay on days since last activity; members active today
     * score 1.0, members away for months approach zero.
     */
    private static function getActivityScore(\stdClass $oCandidate): float
    {
        if (empty($oCandidate->lastActivity)) {
            return 0.0;
        }

        $oLastActivity = \DateTime::createFromFormat('Y-m-d H:i:s', (string)$oCandidate->lastActivity);
        if ($oLastActivity === false) {
            return 0.0;
        }

        $fDays = max(0, (time() - $oLastActivity->getTimestamp()) / 86400);

        return exp(-$fDays / self::ACTIVITY_HALF_LIFE_DAYS);
    }

    private static function getAvatarScore(\stdClass $oCandidate): float
    {
        return empty($oCandidate->avatar) ? 0.0 : 1.0;
    }

    private static function getAge(\stdClass $oProfileData): ?int
    {
        if (empty($oProfileData->birthDate)) {
            return null;
        }

        $oBirthDate = \DateTime::createFromFormat('Y-m-d', (string)$oProfileData->birthDate);
        if ($oBirthDate === false) {
            return null;
        }

        return (new \DateTime())->diff($oBirthDate)->y;
    }

    private static function normalize(string $sValue): string
    {
        return strtolower(trim($sValue));
    }
}
