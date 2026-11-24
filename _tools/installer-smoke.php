#!/usr/bin/env php
<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$sBaseUrl = rtrim(requiredEnvironment('PH7_INSTALLER_BASE_URL'), '/') . '/';
$sInstallToken = requiredEnvironment('PH7_INSTALL_TOKEN');
$sPackageRoot = rtrim(requiredEnvironment('PH7_INSTALLER_PACKAGE_ROOT'), '/');
$sDatabaseHost = requiredEnvironment('PH7_RELEASE_DB_HOST');
$sDatabaseUser = requiredEnvironment('PH7_RELEASE_DB_USER');
$sDatabasePassword = requiredEnvironment('PH7_RELEASE_DB_PASSWORD');
$sDatabaseName = requiredEnvironment('PH7_RELEASE_INSTALL_DB_NAME');
$sDatabasePort = getenv('PH7_RELEASE_DB_PORT') ?: '3306';
$sDatabasePrefix = getenv('PH7_RELEASE_DB_PREFIX') ?: 'ph7_';
$sAdminPassword = getenv('PH7_RELEASE_ADMIN_PASSWORD') ?: 'Gate-Smoke!72-Blue-Quartz';
$sCookiePath = tempnam(sys_get_temp_dir(), 'ph7-installer-cookie-');
if (!is_string($sCookiePath)) {
    fail('Could not create the installer cookie jar.');
}

try {
    $aResponse = request($sBaseUrl . '_install/?a=index', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=index',
        $sCookiePath,
        $aResponse['body'],
        [
            'install_access_token' => $sInstallToken,
            'install_access_submit' => '1'
        ],
        '?a=license'
    );

    $aResponse = request($sBaseUrl . '_install/?a=license', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=license',
        $sCookiePath,
        $aResponse['body'],
        [
            'license_agreed' => 'on',
            'conform_laws_agreed' => 'on',
            'responsibilities_agreed' => 'on',
            'license_agreements_submit' => '1'
        ],
        'name="path_protected"'
    );

    $aResponse = request($sBaseUrl . '_install/?a=config_path', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=config_path',
        $sCookiePath,
        $aResponse['body'],
        ['path_protected' => $sPackageRoot . '/_protected/'],
        'name="db_hostname"'
    );

    $aResponse = request($sBaseUrl . '_install/?a=config_system', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=config_system',
        $sCookiePath,
        $aResponse['body'],
        [
            'db_hostname' => $sDatabaseHost,
            'db_username' => $sDatabaseUser,
            'db_password' => $sDatabasePassword,
            'db_name' => $sDatabaseName,
            'db_prefix' => $sDatabasePrefix,
            'db_charset' => 'utf8mb4',
            'db_port' => $sDatabasePort,
            'ffmpeg_path' => '',
            'bug_report_email' => 'errors@ph7builder.test',
            'config_system_submit' => '1'
        ],
        'name="site_name"'
    );

    $aResponse = request($sBaseUrl . '_install/?a=config_site', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=config_site',
        $sCookiePath,
        $aResponse['body'],
        [
            'site_name' => 'pH7Builder Release Gate',
            'admin_username' => 'releasegate',
            'admin_login_email' => 'login@ph7builder.test',
            'admin_password' => $sAdminPassword,
            'admin_passwords' => $sAdminPassword,
            'admin_first_name' => 'Release',
            'admin_last_name' => 'Verifier',
            'admin_email' => 'admin@ph7builder.test',
            'admin_feedback_email' => 'feedback@ph7builder.test',
            'admin_return_email' => 'noreply@ph7builder.test',
            'config_site_submit' => '1'
        ],
        'name="niche_submit"'
    );

    $aResponse = request($sBaseUrl . '_install/?a=niche', $sCookiePath);
    postStep(
        $sBaseUrl . '_install/?a=niche',
        $sCookiePath,
        $aResponse['body'],
        ['niche_submit' => 'base'],
        'name="confirm_remove_install"'
    );

    $aResponse = request($sBaseUrl . '_install/?a=finish', $sCookiePath);
    $aFinishResponse = request(
        $sBaseUrl . '_install/?a=finish',
        $sCookiePath,
        [
            'confirm_remove_install' => '1',
            'action_token' => actionToken($aResponse['body'])
        ]
    );
    if ($aFinishResponse['status'] !== 200
        || rtrim($aFinishResponse['url'], '/') !== rtrim($sBaseUrl, '/')
        || is_dir($sPackageRoot . '/_install')
    ) {
        fail('The installer did not remove itself and redirect to the installed site.');
    }

    $aHomeResponse = request($sBaseUrl, $sCookiePath);
    if ($aHomeResponse['status'] !== 200
        || stripos($aHomeResponse['body'], 'Fatal error') !== false
        || is_dir($sPackageRoot . '/_install')
    ) {
        fail('The installed homepage did not render successfully.');
    }

    $oDatabase = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $sDatabaseHost, $sDatabasePort, $sDatabaseName),
        $sDatabaseUser,
        $sDatabasePassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $sSchemaVersion = $oDatabase->query(
        sprintf(
            "SELECT version FROM %smodules WHERE vendorName = 'pH7Builder' LIMIT 1",
            $sDatabasePrefix
        )
    )->fetchColumn();
    $iAdminCount = (int)$oDatabase->query(
        sprintf("SELECT COUNT(*) FROM %sadmins WHERE username = 'releasegate'", $sDatabasePrefix)
    )->fetchColumn();

    if ($sSchemaVersion !== '1.6.7' || $iAdminCount !== 1) {
        fail('The installer did not persist the expected schema version and administrator.');
    }

    fwrite(STDOUT, "Fresh installation smoke check passed.\n");
} finally {
    @unlink($sCookiePath);
}

function postStep(
    string $sUrl,
    string $sCookiePath,
    string $sPageBody,
    array $aFields,
    string $sExpectedMarker
): void {
    $aFields['action_token'] = actionToken($sPageBody);
    $aResponse = request($sUrl, $sCookiePath, $aFields);
    if ($aResponse['status'] !== 200 || !str_contains($aResponse['body'], $sExpectedMarker)) {
        fail(sprintf('Installer step failed at %s with HTTP %d.', $sUrl, $aResponse['status']));
    }
}

function request(string $sUrl, string $sCookiePath, ?array $aFields = null): array
{
    $oCurl = curl_init($sUrl);
    if ($oCurl === false) {
        fail('Could not initialise cURL.');
    }

    curl_setopt_array(
        $oCurl,
        [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 8,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_COOKIEFILE => $sCookiePath,
            CURLOPT_COOKIEJAR => $sCookiePath,
            CURLOPT_USERAGENT => 'pH7Builder release gate'
        ]
    );
    if ($aFields !== null) {
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($aFields));
    }

    $sBody = curl_exec($oCurl);
    $iStatus = (int)curl_getinfo($oCurl, CURLINFO_RESPONSE_CODE);
    $sEffectiveUrl = (string)curl_getinfo($oCurl, CURLINFO_EFFECTIVE_URL);
    $sError = curl_error($oCurl);

    if (!is_string($sBody)) {
        fail('Installer request failed: ' . $sError);
    }

    return ['status' => $iStatus, 'body' => $sBody, 'url' => $sEffectiveUrl];
}

function actionToken(string $sHtml): string
{
    $oDocument = new DOMDocument();
    $bPreviousErrorMode = libxml_use_internal_errors(true);
    $bLoaded = $oDocument->loadHTML($sHtml);
    libxml_clear_errors();
    libxml_use_internal_errors($bPreviousErrorMode);
    if (!$bLoaded) {
        fail('Could not parse an installer response.');
    }

    $oXPath = new DOMXPath($oDocument);
    $oToken = $oXPath->query('//input[@name="action_token"]')->item(0);
    if (!$oToken instanceof DOMElement) {
        fail('The installer action token is missing.');
    }

    $sToken = $oToken->getAttribute('value');
    if (preg_match('/^[a-f0-9]{64}$/D', $sToken) !== 1) {
        fail('The installer action token is invalid.');
    }

    return $sToken;
}

function requiredEnvironment(string $sName): string
{
    $sValue = getenv($sName);
    if (!is_string($sValue) || trim($sValue) === '') {
        fail(sprintf('Required environment variable is missing: %s', $sName));
    }

    return trim($sValue);
}

function fail(string $sMessage): never
{
    fwrite(STDERR, $sMessage . PHP_EOL);
    exit(1);
}
