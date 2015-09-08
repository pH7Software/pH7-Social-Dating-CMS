<?php
/**
 * @title            Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Helpers
 */

namespace PH7;

/**
 * Display a basic HTML body page.
 *
 * @param string $sTitle Title of the page.
 * @param string $sMsg Message to display to the page.
 * @return string The HTML body.
 */
function html_body($sTitle, $sMsg)
{
    return '<!DOCTYPE html><head><meta charset="utf-8"><title>' . $sTitle . '</title><meta name="author" content="pH7CMS, Pierre-Henry Soria" /><meta name="copyright" content="(c) 2012-2015, Pierre-Henry Soria. All Rights Reserved" /><meta name="creator" content="pH7CMS, Pierre-Henry Soria" /><meta name="designer" content="pH7CMS, Pierre-Henry Soria" /><meta name="generator" content="pH7CMS" /><style>body{background:#EFEFEF;color:#555;font:normal 12px Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.error,.warning{font-weight:bold;font-size:13px;color:red}.warning{text-transform:uppercase}.italic{font-style:italic}.underline{text-decoration:underline}a{color:#08c;outline-style:none;cursor:pointer}a:link,a:visited{text-decoration:none}a:hover,a:active{color:#F24C9E;text-decoration:underline}</style></head><body><div class="center">' . $sMsg . '</div></body></html>';
}

/**
 * Check Internet Connection.
 *
 * @param string $sCheckHost Default: www.google.com
 * @param boolean $bEnable If FALSE, it disables the checking and force pH7CMS running without an Internet connection. Default: TRUE
 * @return boolean Returns TRUE if the Internet connection is enabled, FALSE otherwise.
 */
function is_internet($sCheckHost = 'www.google.com', $bEnable = PH7_INTERNET_NEEDED)
{
    if (!$bEnable)
        return true;

    return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
}
