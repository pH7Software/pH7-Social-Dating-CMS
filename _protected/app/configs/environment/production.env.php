<?php
/**
 * @title          Production Environment File
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @link           http://ph7builder.com
 * @copyright      (c) 2012-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

define('PH7_ENV_DISABLED', 'Off');

// If php.ini is inadequate, let's fix it
error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', PH7_ENV_DISABLED);
ini_set('display_startup_errors', PH7_ENV_DISABLED);
ini_set('track_errors', PH7_ENV_DISABLED);
ini_set('html_errors', PH7_ENV_DISABLED);


//////////
// SECURITY CHECK
//////////
if (is_dir(PH7_PATH_ROOT . '_install/')) {
    $sMsg = '<p class="warning">Security Alert – <a href="' . Framework\Core\Kernel::SOFTWARE_WEBSITE . '">pH7Builder</a></p>
     <p class="error">Please remove "_install/" folder from your server before continuing.</p>
     <p>You can delete this folder using an FTP client (FileZilla or other) or through cPanel File Manager.</p>
     <p>You can also delete this folder with an SSH connection if your host allows. Below is the UNIX command for doing this:<br />
     <code>sudo rm -rf /YOUR-PUBLIC-SERVER-PATH/YOUR-WEBSITE/_install/</code></p>
     <p>→ Once done, please <a href="' . PH7_URL_ROOT . '">reload pH7Builder</a></p>';

    echo html_body('SECURITY ALERT : Please remove "_install" folder', $sMsg);
    exit;
}
