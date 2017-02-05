<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Install / Info
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

/* Uninstall Conclusion */

// Default content values
$sHtml = '';

$sCode = <<<'EOS'
<li class="menu-item dropdown dropdown-submenu"><a href="{{ $design->url('m/fake-admin-panel', 'admin', 'index') }}">{lang 'Fake Admin Panel'}</a>
  <ul class="dropdown-menu" role="menu">
    <li><a href="{{ $design->url('m/fake-admin-panel', 'admin', 'config') }}">{lang 'Config Fake Admin Panel'}</a></li>
  </ul>
</li>
EOS;


/*** Begin Contents ***/

$sHtml .= '<p>' . t('Uninstallation completed!') . '</p>';
$sHtml .= '<p class="underline">' . t('Please remove the module link from the menu file.') . '</p>';
$sHtml .= '<p>' . t('1) Open the "%0%" file.', '<em>~/templates/themes/base/tpl/top_menu.inc.tpl</em>') . '</p>';
$sHtml .= '<p>' . t('2) Remove the following code.') . '</p>';
$sHtml .= '<textarea cols="65" rows="7" readonly="readonly" onclick="this.focus(); this.select();">' . $sCode . '</textarea>';
$sHtml .= '<p>' . t('3) Done!') . '</p>';
$sHtml .= '<p>' . t('Thank you!') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
