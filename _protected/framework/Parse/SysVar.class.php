<?php
/**
 * @title            SysVar Class
 * @desc             Parse the global pH7CMS variables.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Parse
 * @version          1.3
 */

namespace PH7\Framework\Parse;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Registry\Registry,
PH7\Framework\Core\Kernel,
PH7\Framework\Ip\Ip,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Session\Session;

class SysVar
{

    /**
     * Parser for the System variables.
     *
     * @param string $sVar
     * @return The new parsed text
     */
    public function parse($sVar)
    {
        /*** Not to parse a text ***/
        if (preg_match('/#!.+!#/', $sVar))
        {
            $sVar = str_replace(array('#!', '!#'), '', $sVar);
            return $sVar;
        }

        /***** Site Variables *****/
        $oRegistry = Registry::getInstance();
        $sVar = str_replace('%site_name%', $oRegistry->site_name, $sVar);
        $sVar = str_replace('%url_relative%', PH7_RELATIVE, $sVar);
        $sVar = str_replace(array('%site_url%','%url_root%'), $oRegistry->site_url, $sVar);
        $sVar = str_replace('%url_static%', PH7_URL_STATIC , $sVar);
        unset($oRegistry);

        /***** Affiliate Variables *****/
        $oSession = new Session;
        $sAffUsername = ($oSession->exists('affiliate_username')) ? $oSession->get('affiliate_username') : 'aid';
        $sVar = str_replace('%affiliate_url%', Uri::get('affiliate','router','refer', $sAffUsername), $sVar);
        unset($oSession);

        /***** Global Variables *****/
        $sVar = str_replace('%ip%', Ip::get(), $sVar);

        /***** Kernel Variables *****/
        $sVar = str_replace('%software_name%', Kernel::SOFTWARE_NAME, $sVar);
        $sVar = str_replace('%software_company%', Kernel::SOFTWARE_COMPANY, $sVar);
        $sVar = str_replace('%software_author%', 'Pierre-Henry Soria', $sVar);
        $sVar = str_replace('%software_version_name%', Kernel::SOFTWARE_VERSION_NAME, $sVar);
        $sVar = str_replace('%software_version%', Kernel::SOFTWARE_VERSION, $sVar);
        $sVar = str_replace('%software_build%', Kernel::SOFTWARE_BUILD, $sVar);
        $sVar = str_replace('%software_email%', Kernel::SOFTWARE_EMAIL, $sVar);
        $sVar = str_replace('%software_website%', Kernel::SOFTWARE_WEBSITE, $sVar);

        // Output
        return $sVar;
    }

}
