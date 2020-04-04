<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p><span class="red">' . t('WARNING!') . '</span><br />' . t('Please make a backup of your website and database before proceeding the upgrade.') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
