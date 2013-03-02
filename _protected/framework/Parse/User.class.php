<?php
/**
 * @title            User Class
 * @desc             Parse some User methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.0
 */

namespace PH7\Framework\Parse;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class User
{

    const AT = '@';

    /**
     * Parse the "@<username>" to the link of profile.
     *
     * @static
     * @access public
     * @param string $sContents The contents to parse.
     * @return string The contents with the links to username profiles.
     */
    public static function atUsernameToLink($sContents)
    {
        foreach(static::getAtUsernames($sContents) as $sUsername)
        {
                $sUsernameLink = (new \PH7\UserCore)->getProfileLink($sUsername);
                $sContents = str_replace(static::AT . $sUsername, '<a href="' . $sUsernameLink . '">' . static::AT . $sUsername . '</a>', $sContents);
        }

        return $sContents;
    }

    /**
     * Get the "@<username>" in the contents.
     *
     * @static
     * @access protected
     * @param string $sContents
     * @return array The usernames in an array that were found in the content.
     */
    protected static function getAtUsernames($sContents)
    {
        $aUsername = array();

        if(preg_match_all('#' . static::AT . '('.PH7_USERNAME_PATTERN.'{'.DbConfig::getSetting('minUsernameLength').','.PH7_MAX_USERNAME_LENGTH.'})#u', $sContents, $aMatches, PREG_PATTERN_ORDER))
        {
            foreach($aMatches[1] as $sUsername)
            {
                if((new \PH7\ExistsCoreModel)->username($sUsername))
                {
                    $aUsername[] = $sUsername;
                    $aUsername = array_unique($aUsername); // Delete duplicate usernames.
                }
            }
        }

        return $aUsername;
    }

}
