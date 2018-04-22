<?php
/**
 * @title          Autocomplete Username File
 * @desc           This file can suggest a list of user name with jQuery and Ajax.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Session\Session;

// Only for members
if (UserCore::auth()) {
    $oHttpRequest = new Http;

    if ($oHttpRequest->postExists('username')) {
        if ($oUsernameResult = (new UserCoreModel)->getUsernameList($oHttpRequest->post('username'))) {
            // XML tags
            echo '<users><ul>';
            foreach ($oUsernameResult as $oList) {
                // Do not include the user profile that is connected since it doesn't make sense.
                if ($oList->profileId == (new Session)->get('member_id')) break;

                echo '<li>
                        <username>', escape($oList->username, true), '</username>
                        <avatar>', (new Design)->getUserAvatar($oList->username, $oList->sex, 32), '</avatar>
                      </ul>';
            }
            echo '</ul></users>';
        }
    }

    unset($oHttpRequest);
}
