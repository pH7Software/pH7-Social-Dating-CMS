<?php
/**
 * @title          Autocomplete Username File
 * @desc           This file can suggest a list of user name with jQuery and Ajax.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Session\Session;

const AVATAR_SIZE = 32;

// Only for members
if (UserCore::auth()) {
    $oHttpRequest = new HttpRequest;

    if ($oHttpRequest->postExists('username')) {
        if ($oUsernameResult = (new UserCoreModel)->getUsernameList($oHttpRequest->post('username'))) {
            echo '<users><ul>';
            foreach ($oUsernameResult as $oList) {
                // Don't include the current signed in user profile, won't make sense for the user to see their-self
                if ($oList->profileId == (new Session)->get('member_id')) break;

                echo '<li>
                        <username>', escape($oList->username, true), '</username>
                        <avatar>', (new Design)->getUserAvatar($oList->username, $oList->sex, AVATAR_SIZE), '</avatar>
                      </ul>';
            }
            echo '</ul></users>';
        }
    }

    unset($oHttpRequest);
}
