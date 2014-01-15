<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p><span class="red">' . t('WARNING!') . '</span><br />' . t('Please make a backup of your site and your database before proceeding with upgrade!') . '</p>';
$sHtml .= '<p class="red">' . t('Be careful if you have a lot of members, please, stay with your current version for all your members will reset their password.') . '<br />' .
t('Also for you (and other admins), be sure to reset your password with the new system hash.') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
