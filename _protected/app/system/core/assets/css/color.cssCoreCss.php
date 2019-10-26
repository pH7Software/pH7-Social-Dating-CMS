<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Css
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

$sBackgroundColor = DbConfig::getSetting('backgroundColor');
$sTextColor = DbConfig::getSetting('textColor');
$sHeading1Color = DbConfig::getSetting('heading1Color');
$sHeading2Color = DbConfig::getSetting('heading2Color');
$sHeading3Color = DbConfig::getSetting('heading3Color');
$sLinkColor = DbConfig::getSetting('linkColor');
$sFooterLinkColor = DbConfig::getSetting('footerLinkColor');
$sLinkHoverColor = DbConfig::getSetting('linkHoverColor');

if (!empty($sBackgroundColor)) {
    printf('body {background-color: %s !important; background-image: none !important}', $sBackgroundColor);
}

if (!empty($sTextColor)) {
    printf('body {color: %s !important}', $sTextColor);
}

if (!empty($sHeading1Color)) {
    printf('h1 {color: %s !important}', $sHeading1Color);
}

if (!empty($sHeading2Color)) {
    printf('h2 {color: %s !important}', $sHeading2Color);
}

if (!empty($sHeading3Color)) {
    printf('h3 {color: %s !important}', $sHeading3Color);
}

if (!empty($sLinkColor)) {
    printf('a {color: %s !important}', $sLinkColor);
}

if (!empty($sFooterLinkColor)) {
    printf('footer a {color: %s !important}', $sFooterLinkColor);
}

if (!empty($sLinkHoverColor)) {
    printf('a:hover, a:active {color: %s !important}', $sLinkHoverColor);
}
