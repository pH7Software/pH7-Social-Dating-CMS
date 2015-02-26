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
 * @param string $sMsg Message to display to the page.
 * @return string The HTML body.
 */
function html_body($sMsg)
{
    return '<!DOCTYPE html><head><title>Rewrite Mod Test</title></head><body><div style="margin-left:auto;margin-right:auto;width:80%;text-align:center">' . $sMsg . '</div></body></html>';   
}

/**
 * Check Internet Connection.
 *
 * @param string $sCheckHost Default: www.google.com
 * @return boolean Returns TRUE if the Internet connection is enabled, FALSE otherwise.
 */
function is_internet($sCheckHost = 'www.google.com')
{
	return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
}