<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License.
 * @package        PH7 / App / Module / Fake Admin Panel / Install / Info
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

/* Uninstall Conclusion */

// Default content value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p>' . t('Uninstall completed!') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
