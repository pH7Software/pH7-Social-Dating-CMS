<?php
/**
 * @title            Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Helpers
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/**
 * Display a basic HTML body page.
 * Since it will display a dynamic page,
 * it will also send headers to not cache the page
 *
 * @param string $sTitle Title of the page.
 * @param string $sMsg Message to display to the page.
 *
 * @return string The HTML body.
 */
function html_body($sTitle, $sMsg)
{
    // Send headers to not cache the page
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

    return '<!DOCTYPE html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><title>' . $sTitle . '</title><meta name="author" content="pH7CMS, Pierre-Henry Soria"><meta name="copyright" content="(c) 2012-' . date('Y') . ', Pierre-Henry Soria. All Rights Reserved"><meta name="creator" content="pH7CMS, Pierre-Henry Soria"><meta name="designer" content="pH7CMS, Pierre-Henry Soria"><meta name="generator" content="pH7CMS"><style>body{background:#EFEFEF;color:#555;font:normal 12px Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.error,.warning{font-weight:bold;font-size:13px;color:red}.warning{text-transform:uppercase}.italic{font-style:italic}.underline{text-decoration:underline}a{color:#08c;outline-style:none;cursor:pointer}a:link,a:visited{text-decoration:none}a:hover,a:active{color:#F24C9E;text-decoration:underline}</style></head><body><div class="center">' . $sMsg . '</div></body></html>';
}

/**
 * Check Internet Connection.
 *
 * @return bool Returns TRUE if the Internet connection is enabled, FALSE otherwise.
 */
function is_internet()
{
    // Sometimes, hosts ban a domain name, so check with several random domain name in case this happened
    $aRandomHosts = ['www.google.com', 'www.bing.com', 'www.yahoo.com', 'www.facebook.com', 'twitter.com'];

    // Use random domain from the array to avoid a loop (it's fine for this usage. At worst the user will have to reload twice the page)
    return (bool)@fsockopen($aRandomHosts[mt_rand(0, 4)], 80, $iErrno, $sErrStr, 5);
}
