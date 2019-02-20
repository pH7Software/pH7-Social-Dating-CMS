<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;

final class MessageGenerator
{
    /**
     * @return string A random patron paragraph.
     */
    public static function getPatreonParagraph()
    {
        $aParagraphs = self::getParagraphs();

        return $aParagraphs[array_rand($aParagraphs)];
    }

    /**
     * @return array
     */
    private static function getParagraphs()
    {
        $aParagraphs = [
            t('Do you think it is the right time to <a href="%0%">Become a Patron</a> and support the development of the software?', Kernel::PATREON_URL),
            t('Are you generous enough to thank the development of the software? <a href="%0%">Become a patron</a> today.', Kernel::PATREON_URL),
            t('Subscribe to <a href="%0%">pH7CMS Patreon</a>.', Kernel::PATREON_URL),
            t('<a href="%0%">Subscribe to be a patron</a>.', Kernel::PATREON_URL)
        ];

        return $aParagraphs;
    }
}
