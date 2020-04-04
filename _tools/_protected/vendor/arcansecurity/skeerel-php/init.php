<?php
/**
 * Created by Florian Pradines
 * @see https://stackoverflow.com/a/2692394
 */

$dir      = new RecursiveDirectoryIterator(dirname(__FILE__) . '/Skeerel');
$iterator = new RecursiveIteratorIterator($dir);
foreach ($iterator as $file) {
    $fname = $file->getFilename();
    if (preg_match('%\.php$%', $fname)) {
        require_once($file->getPathname());
    }
}