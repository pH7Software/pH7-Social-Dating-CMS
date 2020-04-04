<?php
defined('PH7') or exit('Restricted access');
if (!\PH7\Admin::auth()) exit('Restricted access'); // Accessible only for admins

error_reporting(0); // Set E_ALL for debuging

require_once __DIR__ . DIRECTORY_SEPARATOR . 'elFinderConnector.class.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'elFinder.class.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'elFinderVolumeDriver.class.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'elFinderVolumeLocalFileSystem.class.php';

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
    return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
        ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
        :  null;                                    // else elFinder decide it itself
}

$opts = array(
    // 'debug' => true,
    'roots' => array(
        array(
            'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
            'path'          => PH7_PATH_PROTECTED,       // path to files (REQUIRED)
            'URL'           => '',        // URL to files (REQUIRED) - No URL because this part is not accessible to Web browsers
            'accessControl' => 'access'             // disable and hide dot starting files (OPTIONAL)
        )
    )
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
