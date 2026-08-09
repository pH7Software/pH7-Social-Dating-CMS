<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Install
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$sTokenPath = __DIR__ . '/data/caches/install-token.hash';
$bRotate = in_array('--rotate', $argv, true);
if (is_file($sTokenPath) && !$bRotate) {
    fwrite(STDERR, "An installer token already exists. Use --rotate only if the previous token is unavailable.\n");
    exit(1);
}

$sToken = bin2hex(random_bytes(32));
$sTemporaryPath = $sTokenPath . '.creating-' . bin2hex(random_bytes(8));
if (file_put_contents($sTemporaryPath, hash('sha256', $sToken) . PHP_EOL, LOCK_EX) === false) {
    fwrite(STDERR, "Could not write the installer token. Check _install/data permissions.\n");
    exit(1);
}

chmod($sTemporaryPath, 0640);
if (!rename($sTemporaryPath, $sTokenPath)) {
    unlink($sTemporaryPath);
    fwrite(STDERR, "Could not finalize the installer token. Check _install/data permissions.\n");
    exit(1);
}

fwrite(STDOUT, "Installer access token (shown once):\n{$sToken}\n");
