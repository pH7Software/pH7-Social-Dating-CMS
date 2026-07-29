<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

/**
 * Item-based collaborative filtering over member interactions
 * ("members who liked the same profiles as you also liked ...").
 *
 * Pure computation on purpose: interactions come in as plain arrays, so the scoring is
 * deterministic, unit-testable, and independent of where the signals are stored
 * (hotornot votes, likes, profile views, ...). Everything runs locally — no external APIs.
 */
final class BehavioralAffinityCore
{
    private const DEFAULT_MAX_RESULTS = 10;

    /**
     * @param array $aInteractions flat list of [actorProfileId, targetProfileId] pairs
     * @param int   $iProfileId    the member to compute recommendations for
     *
     * @return array map of [targetProfileId => affinity score], best first
     */
    public static function getAffinityScores(array $aInteractions, int $iProfileId, int $iMaxResults = self::DEFAULT_MAX_RESULTS): array
    {
        $aTargetsByActor = self::groupTargetsByActor($aInteractions);

        if (empty($aTargetsByActor[$iProfileId])) {
            return []; // No interaction history yet (cold start) => let the content score take over
        }

        $aMyTargets = $aTargetsByActor[$iProfileId];
        $aScores = [];

        foreach ($aTargetsByActor as $iActorId => $aTheirTargets) {
            if ($iActorId === $iProfileId) {
                continue;
            }

            $iSharedTastes = count(array_intersect_key($aTheirTargets, $aMyTargets));
            if ($iSharedTastes === 0) {
                continue; // No taste overlap with this member
            }

            /*
             * The more of their likes we share, the more their other likes count;
             * normalizing by their activity keeps hyperactive members from dominating.
             */
            $fWeight = $iSharedTastes / count($aTheirTargets);

            foreach ($aTheirTargets as $iTargetId => $bUnused) {
                if ($iTargetId === $iProfileId || isset($aMyTargets[$iTargetId])) {
                    continue; // Skip self and profiles already interacted with
                }

                $aScores[$iTargetId] = ($aScores[$iTargetId] ?? 0.0) + $fWeight;
            }
        }

        arsort($aScores);

        return array_slice($aScores, 0, $iMaxResults, true);
    }

    /**
     * @param array $aInteractions flat list of [actorProfileId, targetProfileId] pairs
     *
     * @return array map of [actorProfileId => [targetProfileId => true]] (sets, for O(1) lookups)
     */
    private static function groupTargetsByActor(array $aInteractions): array
    {
        $aTargetsByActor = [];

        foreach ($aInteractions as $aPair) {
            if (!isset($aPair[0], $aPair[1])) {
                continue; // Ignore malformed rows defensively
            }

            $iActorId = (int)$aPair[0];
            $iTargetId = (int)$aPair[1];

            if ($iActorId === $iTargetId) {
                continue; // Self-interactions carry no signal
            }

            $aTargetsByActor[$iActorId][$iTargetId] = true;
        }

        return $aTargetsByActor;
    }
}
