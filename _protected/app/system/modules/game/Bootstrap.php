<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game
 */
namespace PH7;
defined('PH7') or die('Restricted access');

// If the Games are not installed (no game folders) and the administrator is not logged in to add games, we will display a Not Found page with an explanatory message.
if (!AdminCore::auth())
{
    $sGamePath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/file';
    $sThumbPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/img/thumb';

    if((!(is_dir($sGamePath) && is_dir($sThumbPath))) || !(filesize($sGamePath) > 5000 && filesize($sThumbPath) > 5000))
        (new Controller)->displayPageNotFound(t('Sorry, but no games seem to be installed at time.'), false); // We disable the HTTP error code 404 for Ajax requests running
}
