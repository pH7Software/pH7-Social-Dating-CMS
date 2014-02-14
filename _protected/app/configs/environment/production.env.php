<?php
/**
 * @title          Production Environment File
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @link           http://software.hizup.com
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
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
ini_set('display_errors' , 'Off');
ini_set('display_startup_errors', 'Off');
ini_set('track_errors', 'Off');
ini_set('html_errors', 'Off');

if (is_dir(PH7_PATH_ROOT .'_install/'))
{
    echo
    '<!doctype html><html><head><meta charset="utf-8"><title>SECURITY ALERT : Please remove "_install" folder</title><style>body{background:#EFEFEF;color:#555;font:normal 12px Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.error,.warning{font-weight:bold;font-size:13px;color:red}.warning{text-transform:uppercase}.italic{font-style:italic}.underline{text-decoration:underline}a{color:#08c;outline-style:none;cursor:pointer}a:link,a:visited{text-decoration:none}a:hover,a:active{color:#F24C9E;text-decoration:underline}</style></head><body><div class="center">
     <p class="warning">Security alert</p>
     <p class="error">Please remove the following from your server before continuing: "_install/" folder. Then, click "Reload pH7 Dating Social CMS" below to continue.</p>
     <p>You can delete this folder using an FTP client (FileZilla or other).</p>
     <p>You can also delete this folder with an SSH connection if your host allows. The UNIX command to do this is:<br />
     <code>sudo rm -rf /YOUR-PUBLIC-SERVER-PATH/YOUR-WEBSITE/_install/</code></p>
     <p>After doing this, please <a href="' . PH7_URL_ROOT . '">reload pH7CMS</a></p></div></body></html>';
    exit;
}
