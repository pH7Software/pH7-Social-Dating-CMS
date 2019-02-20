<?php
/**
 * @title          Production Environment File
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @link           http://ph7cms.com
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / Config / Environment
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/************************/
// SECURITY CHECK
/************************/

// If php.ini is inadequate, we fix it.
error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
ini_set('track_errors', 'Off');
ini_set('html_errors', 'Off');

if (is_dir(PH7_PATH_ROOT . '_install/')) {
    $sMsg = '<p class="warning">Security Alert – <a href="' . Framework\Core\Kernel::SOFTWARE_WEBSITE . '">pH7CMS</a></p>
     <p class="error">Please remove "_install/" folder from your server before continuing.</p>
     <p>You can delete this folder using an FTP client (FileZilla or other).</p>
     <p>You can also delete this folder with an SSH connection if your host allows. Below is the UNIX command to do this is:<br />
     <code>sudo rm -rf /YOUR-PUBLIC-SERVER-PATH/YOUR-WEBSITE/_install/</code></p>
     <p>After doing this, please <a href="' . PH7_URL_ROOT . '">reload pH7CMS</a></p>';

    echo html_body('SECURITY ALERT : Please remove "_install" folder', $sMsg);
    exit;
}
