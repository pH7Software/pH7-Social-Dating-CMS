<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
    public static function getDefault($sLangId)
    {
        $aData = [
            'langId' => $sLangId,
            'pageTitle' => 'Home',
            'metaDescription' => 'The Best Online Social Dating Service to meet people and keep in touch with your friends',
            'metaKeywords' => 'meet people, community, single, friends, meet singles, women, men, dating site, dating service, dating website, online dating website',
            'headline' => 'Be on the right place!',
            'slogan' => 'Online Dating Community with Chat Rooms',
            'promoText' => 'You\'re on the best place for meeting new people nearby! Chat, Flirt, Socialize and have Fun!<br />Create any Dating Sites like that with <a href="https://ph7cms.com">pH7CMS</a>. It is Professional, Free, Open Source, ...',
            'metaRobots' => 'index, follow, all',
            'metaAuthor' => 'Pierre-Henry Soria (pH7CMS.com)',
            'metaCopyright' => 'Pierre-Henry Soria. All Rights Reserved.',
            'metaRating' => 'general',
            'metaDistribution' => 'global',
            'metaCategory' => 'dating'
        ];

        return $aData;
    }
}
