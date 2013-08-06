<?php
namespace PH7;
defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p><span class="red">' . t('WARNING!') . '</span><br />' . t('Please make a backup of your site and your database before proceeding with upgrade!') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
