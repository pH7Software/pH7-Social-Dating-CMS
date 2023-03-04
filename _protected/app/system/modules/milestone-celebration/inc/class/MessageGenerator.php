<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;

final class MessageGenerator
{
    /**
     * @return string A random patron paragraph.
     */
    public static function getPatronParagraph()
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
            t('Subscribe now to <a href="%0%">%1% Patreon</a>.', Kernel::PATREON_URL, Kernel::SOFTWARE_NAME),
            t('<a href="%0%">Subscribe to be a patron</a>.', Kernel::PATREON_URL),
            t('Make your website better by <a href="%0%">offering me a coffee</a> to caffeine boost the development ☕️', Kernel::BUYMEACOFFEE_URL),
            t('Buy the author of the software <a href="%0%">a latte</a> to thank him for %1%️', Kernel::BUYMEACOFFEE_URL, Kernel::SOFTWARE_NAME),
        ];

        return $aParagraphs;
    }
}
