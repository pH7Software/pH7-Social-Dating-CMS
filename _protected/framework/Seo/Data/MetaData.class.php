<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2019-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Seo / Data
 */

namespace PH7\Framework\Seo\Data;

final class MetaData
{
    /**
     * @param string $sLangId The new language key (e.g., de_DE, fr_FR).
     *
     * @return array
     */
    public static function getDefault(string $sLangId): array
    {
        $aData = [
            'langId' => $sLangId,
            'pageTitle' => 'Home',
            'metaDescription' => 'Meet people, build genuine connections, and grow a community around your brand.',
            'metaKeywords' => 'meet people, community, matchmaking, social network, dating',
            'headline' => 'Welcome to our community',
            'slogan' => 'Meet people. Build genuine connections.',
            'promoText' => 'Create your profile, discover people nearby, and start a conversation.',
            'metaRobots' => 'index, follow, all',
            'metaAuthor' => 'Your Site Name',
            'metaCopyright' => 'Your Site Name',
            'metaRating' => 'general',
            'metaDistribution' => 'global',
            'metaCategory' => 'dating'
        ];

        return $aData;
    }
}
