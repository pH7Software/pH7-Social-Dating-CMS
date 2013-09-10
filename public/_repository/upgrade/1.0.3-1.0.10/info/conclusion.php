<?php
namespace PH7;
defined('PH7') or exit('Restricted access');

// Default contents value
$sHtml = '';

/*** Begin Contents ***/

$sHtml .= '<p>' . t('Done!') . '</p>';
$sHtml .= '<p class="red">' . t('Now you need to delete the following files via FTP or SSH:') . '</p>';
$sHtml .= '<pre>';
$sHtml .= PH7_PATH_FRAMEWORK . 'Mvc' . PH7_DS . 'Request' . PH7_DS . 'HttpRequest.class.php' . "\n";
$sHtml .= PH7_PATH_FRAMEWORK . 'Mvc' . PH7_DS . 'Router' . PH7_DS . 'UriRoute.class.php';
$sHtml .= '</pre>';
$sHtml .= '<p>' . t('Good luck :-)') . '</p>';

/*** End Contents ***/

// Output!
return $sHtml;
