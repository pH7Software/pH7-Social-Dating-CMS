<?php

/**
 * Routes rewritten application URLs when release checks use PHP's built-in server.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

$sRequestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$sRequestPath = is_string($sRequestPath) ? rawurldecode($sRequestPath) : '/';
$sPublicPath = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), DIRECTORY_SEPARATOR);
$sRequestedFile = $sPublicPath . $sRequestPath;

if ($sRequestPath !== '/' && (is_file($sRequestedFile) || is_dir($sRequestedFile))) {
    return false;
}

require $sPublicPath . '/index.php';
