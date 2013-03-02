<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License.
 * @package        PH7 / App / Module / Fake Admin Panel / Install / Info
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

/* Install Conclusion */

// Default values variables
$sHtml = '';

$sCode = <<<'EOS'
<li><a href="{{ $design->url('m/fake-admin-panel', 'admin', 'index') }}">{@lang('Fake Admin Panel')@}</a>
  <ul>
    <li><a href="{{ $design->url('m/fake-admin-panel', 'admin', 'config') }}">{@lang('Config Fake Admin Panel')@}</a></li>
  </ul>
</li>
EOS;

$sFindCode = <<<'EOS'
'{@lang('Universal Login Config')@}</a></li>'
EOS;


/*** Begin Contents ***/

$sHtml .= '<p>' . t('The installation is finished.') . '</p>';
$sHtml .= '<p class="underline">' . t('Please add the link to the administration of the module in the file menu.') . '</p>';
$sHtml .= '<p>' . t('1) Open the "%0%" file.', '<em>~/templates/themes/base/tpl/top_menu.inc.tpl</em>') . '</p>';
$sHtml .= '<p>' . t('2) Find this piece code "%0%" and add the following code after it.', escape($sFindCode)) . '</p>';
$sHtml .= '<textarea cols="65" rows="7" readonly="readonly" onclick="this.focus(); this.select();">' . $sCode . '</textarea>';
$sHtml .= '<p>' . t('3) Save the file and to got in your real admin panel for the configuration.') . '</p>';
$sHtml .= '<p>' . t('4) Done!') . '</p>';
$sHtml .= '<p>' . t('Thank you for using our module!') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
