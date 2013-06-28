<?php
namespace PH7;
defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p>' . t('Done!') . '</p>';
$sHtml .= '<p class="red">' . t('Warning, this is not finished yet ;-)') . '</p>';
$sHtml .= '<p>';
$sHtml .= t('Edit your "%0%" file.', PH7_PATH_APP_CONFIG . 'config.ini') . '<br />';
$sHtml .= t('Add M after the size of the "upload.max_size" variable (e.g., this: "upload.max_size = 500M" instead of "upload.max_size = 500"') . '<br /><br />';
$sHtml .= t('Good luck :-)');
$sHtml .= '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
