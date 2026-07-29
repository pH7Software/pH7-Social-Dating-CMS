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

    /** Share of the final score taken by behavioral affinity (BehavioralAffinityCore) when available */
    public const WEIGHT_BEHAVIOR = 0.3;

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
     * Like rank(), but blends in behavioral affinity scores when they're available
     * (e.g. computed by BehavioralAffinityCore from likes/votes/views).
     * With no affinity data (cold start), this is identical to rank().
     *
     * @param \stdClass   $oProfile        the reference profile
     * @param \stdClass[] $aCandidates     rows as returned by UserCoreModel::search()
     * @param array       $aAffinityScores map of [profileId => affinity score] from BehavioralAffinityCore
     *
     * @return \stdClass[] the candidates sorted by descending blended score, cut to $iLimit
     */
    public static function rankBlended(\stdClass $oProfile, array $aCandidates, array $aAffinityScores, int $iLimit): array
    {
        if (empty($aAffinityScores)) {
            return self::rank($oProfile, $aCandidates, $iLimit);
        }

        // Normalize affinities to the 0..1 range so they're comparable with the content score
        $fMaxAffinity = (float)max($aAffinityScores);

        usort($aCandidates, static function (\stdClass $oA, \stdClass $oB) use ($oProfile, $aAffinityScores, $fMaxAffinity): int {
            return self::getBlendedScore($oProfile, $oB, $aAffinityScores, $fMaxAffinity) <=> self::getBlendedScore($oProfile, $oA, $aAffinityScores, $fMaxAffinity);
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

    private static function getBlendedScore(\stdClass $oProfile, \stdClass $oCandidate, array $aAffinityScores, float $fMaxAffinity): float
    {
        $iCandidateId = (int)($oCandidate->profileId ?? 0);
        $fBehavior = $fMaxAffinity > 0 && isset($aAffinityScores[$iCandidateId])
            ? (float)$aAffinityScores[$iCandidateId] / $fMaxAffinity
            : 0.0;

        return (1 - self::WEIGHT_BEHAVIOR) * self::score($oProfile, $oCandidate) + self::WEIGHT_BEHAVIOR * $fBehavior;
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
        // Match the app's definition of a usable photo (see UserCoreModel): a set avatar that moderation
        // approved. A pending/rejected photo shouldn't earn the "has a real photo" boost. When the
        // approval flag is absent from the row, default to approved to mirror the column's DB default.
        $bApproved = !isset($oCandidate->approvedAvatar) || (int)$oCandidate->approvedAvatar === 1;

        return !empty($oCandidate->avatar) && $bApproved ? 1.0 : 0.0;
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

        $oNow = new \DateTime();
        // DateInterval::$y is absolute, so a future (corrupt) birthDate would yield a bogus positive
        // age. Treat that as unknown rather than feeding a wrong age into the affinity curve.
        if ($oBirthDate > $oNow) {
            return null;
        }

        return $oNow->diff($oBirthDate)->y;
    }

    private static function normalize(string $sValue): string
    {
        // City is free-typed, so collapse internal whitespace too ("New  York" == "New York")
        // before comparing; otherwise honest same-city matches are silently missed.
        return strtolower(trim(preg_replace('/\s+/', ' ', $sValue)));
    }
}
