<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit
 */

echo 'ℹ️ GD installed: ' . (extension_loaded('gd') ? '✅' : '❌') . PHP_EOL;
echo 'ℹ️ Zip installed: ' . (extension_loaded('zip') ? '✅' : '❌') . PHP_EOL;
echo 'ℹ️ Zlib installed: ' . (extension_loaded('zlib') ? '✅' : '❌') . PHP_EOL;
echo 'ℹ️ mbstring installed: ' . (extension_loaded('mbstring') ? '✅' : '❌') . PHP_EOL;
echo 'ℹ️ exif installed: ' . (extension_loaded('exif') ? '✅' : '❌') . PHP_EOL;
