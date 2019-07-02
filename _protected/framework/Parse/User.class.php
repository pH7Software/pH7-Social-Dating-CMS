<?php
/**
 * @title            User Class
 * @desc             Parse some User methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 */

namespace PH7\Framework\Parse;

defined('PH7') or exit('Restricted access');

use PH7\ExistsCoreModel;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\UserCore;

class User
{
    const AT = '@';

    /**
     * Parse the "@<username>" to the link of profile.
     *
     * @param string $sContents The contents to parse.
     *
     * @return string The contents with the links to username profiles.
     */
    public static function atUsernameToLink($sContents)
    {
        foreach (self::getAtUsernames($sContents) as $sUsername) {
            $sUsernameLink = (new UserCore)->getProfileLink($sUsername);

            $sContents = str_replace(
                static::AT . $sUsername,
                '<a href="' . $sUsernameLink . '">' . static::AT . $sUsername . '</a>',
                $sContents
            );
        }

        return $sContents;
    }

    /**
     * Get the "@<username>" in the contents.
     *
     * @param string $sContents
     *
     * @return \Generator
     */
    private static function getAtUsernames($sContents)
    {
        if (self::areProfileFound($sContents, $aMatches)) {
            // Delete duplicate usernames
            $aMatches[1] = array_unique($aMatches[1]);

            foreach ($aMatches[1] as $sUsername) {
                if ((new ExistsCoreModel)->username($sUsername)) {
                    yield $sUsername; // "yield" thanks to PHP 5.5
                }
            }
        }
    }

    /**
     * @param string $sContents
     * @param array $aMatches
     *
     * @return false|int
     */
    private static function areProfileFound($sContents, &$aMatches)
    {
        return preg_match_all(
            '#' . static::AT . '(' . PH7_USERNAME_PATTERN . '{' . DbConfig::getSetting('minUsernameLength') . ',' . DbConfig::getSetting('maxUsernameLength') . '})#u',
            $sContents,
            $aMatches,
            PREG_PATTERN_ORDER
        );
    }
}
