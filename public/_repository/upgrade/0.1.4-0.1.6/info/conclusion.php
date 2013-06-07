<?php
namespace PH7;
defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p>' . t('Done!') . '<br />';
$sHtml .= t('Please also delete all the cache files for the changes to take effect.') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
