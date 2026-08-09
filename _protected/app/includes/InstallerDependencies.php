<?php

/**
 * Install the web installer's constrained Composer dependency when the installer is present.
 *
 * The installer must be removed after setup. Subsequent root Composer operations therefore
 * need to skip its dependencies instead of failing because the directory no longer exists.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

const INSTALLER_COMPOSER_FILE = '_install/composer.json';

$sProjectRoot = dirname(__DIR__, 3);
$sInstallerComposerFile = $sProjectRoot . DIRECTORY_SEPARATOR . INSTALLER_COMPOSER_FILE;

if (!is_file($sInstallerComposerFile)) {
    fwrite(STDOUT, "The installer has already been removed; skipping its dependencies.\n");
    exit(0);
}

$sComposerBinary = getenv('COMPOSER_BINARY');
if (!is_string($sComposerBinary) || $sComposerBinary === '') {
    $sComposerBinary = 'composer';
}

$aCommand = [
    $sComposerBinary,
    'install',
    '--working-dir=' . dirname($sInstallerComposerFile),
    '--no-interaction',
    '--no-progress',
    '--prefer-dist',
    '--optimize-autoloader'
];

if (getenv('COMPOSER_DEV_MODE') === '0') {
    $aCommand[] = '--no-dev';
}

$sCommand = implode(' ', array_map('escapeshellarg', $aCommand));
passthru($sCommand, $iExitCode);

if ($iExitCode !== 0) {
    fwrite(STDERR, "Unable to install the web installer's declared dependencies.\n");
}

exit($iExitCode);
