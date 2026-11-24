#!/usr/bin/env php
<?php

/**
 * Audits representative pages from a freshly installed release package.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

const MAX_ROUND_TRIP_SECONDS = 4.0;
const MEMBER_EMAIL = 'release-member@ph7builder.test';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$sBaseUrl = rtrim(requiredEnvironment('PH7_PERFORMANCE_BASE_URL'), '/');
$sPackageRoot = rtrim(requiredEnvironment('PH7_PERFORMANCE_PACKAGE_ROOT'), '/');
$sMemberPassword = requiredEnvironment('PH7_RELEASE_MEMBER_PASSWORD');
$sConfigPath = $sPackageRoot . '/_protected/app/configs/config.ini';
$sOriginalConfig = file_get_contents($sConfigPath);
$sCookiePath = tempnam(sys_get_temp_dir(), 'ph7-performance-cookie-');

if (!is_string($sOriginalConfig) || !is_string($sCookiePath)) {
    fail('The installed application audit could not initialise its configuration or cookie jar.');
}

$aJourneys = [
    'homepage' => ['path' => '/', 'max_queries' => 80, 'marker' => null],
    'member login' => ['path' => '/login', 'max_queries' => 60, 'marker' => 'form_login_user'],
    'member discovery' => ['path' => '/meet-people', 'max_queries' => 100, 'marker' => 'release-profile'],
    'public profile' => ['path' => '/@release-profile', 'max_queries' => 120, 'marker' => 'release-profile']
];
$aAuthenticatedJourneys = [
    'member dashboard' => ['path' => '/user/account/index', 'max_queries' => 120, 'marker' => 'release-member'],
    'member inbox' => ['path' => '/mail', 'max_queries' => 100, 'marker' => null]
];

try {
    enableDevelopmentMode($sConfigPath, $sOriginalConfig);

    foreach ($aJourneys as $sName => $aJourney) {
        auditPage($sName, $sBaseUrl, $sCookiePath, $aJourney);
    }

    signIn($sBaseUrl, $sCookiePath, $sMemberPassword);

    foreach ($aAuthenticatedJourneys as $sName => $aJourney) {
        auditPage($sName, $sBaseUrl, $sCookiePath, $aJourney, true);
    }

    fwrite(STDOUT, "Installed application performance audit passed.\n");
} finally {
    file_put_contents($sConfigPath, $sOriginalConfig);
    @unlink($sCookiePath);
}

function auditPage(
    string $sName,
    string $sBaseUrl,
    string $sCookiePath,
    array $aJourney,
    bool $bRequiresAuthentication = false
): void {
    $aResponse = request($sBaseUrl . $aJourney['path'], $sCookiePath);
    if ($aResponse['status'] !== 200) {
        fail(sprintf('%s returned HTTP %d.', ucfirst($sName), $aResponse['status']));
    }
    if (containsPhpFailure($aResponse['body'])) {
        fail(sprintf('%s exposed a PHP failure.', ucfirst($sName)));
    }
    if ($bRequiresAuthentication && str_contains($aResponse['body'], 'form_login_user')) {
        fail(sprintf('%s redirected the authenticated member to sign in.', ucfirst($sName)));
    }
    if (is_string($aJourney['marker']) && !str_contains($aResponse['body'], $aJourney['marker'])) {
        fail(sprintf('%s did not render its expected content.', ucfirst($sName)));
    }

    $aMetrics = performanceMetrics($aResponse['body']);
    if ($aMetrics['queries'] > $aJourney['max_queries']) {
        fail(
            sprintf(
                '%s used %d queries, above its %d-query budget.',
                ucfirst($sName),
                $aMetrics['queries'],
                $aJourney['max_queries']
            )
        );
    }
    if ($aResponse['seconds'] > MAX_ROUND_TRIP_SECONDS) {
        fail(
            sprintf(
                '%s took %.3f seconds, above its %.1f-second round-trip budget.',
                ucfirst($sName),
                $aResponse['seconds'],
                MAX_ROUND_TRIP_SECONDS
            )
        );
    }

    fwrite(
        STDOUT,
        sprintf(
            "PASS: %s (%d queries, %s application time, %.3fs round trip)\n",
            $sName,
            $aMetrics['queries'],
            $aMetrics['application_time'],
            $aResponse['seconds']
        )
    );
}

function signIn(string $sBaseUrl, string $sCookiePath, string $sMemberPassword): void
{
    $aLoginPage = request($sBaseUrl . '/login', $sCookiePath);
    $aFields = hiddenFormFields($aLoginPage['body'], 'form_login_user');
    $aFields['mail'] = MEMBER_EMAIL;
    $aFields['password'] = $sMemberPassword;

    $aLoginResponse = request($sBaseUrl . '/login', $sCookiePath, $aFields);
    if ($aLoginResponse['status'] !== 200 || containsPhpFailure($aLoginResponse['body'])) {
        fail('The release member could not submit the sign-in form.');
    }
}

function hiddenFormFields(string $sHtml, string $sFormId): array
{
    $oDocument = new DOMDocument();
    $bPreviousErrorMode = libxml_use_internal_errors(true);
    $bLoaded = $oDocument->loadHTML($sHtml);
    libxml_clear_errors();
    libxml_use_internal_errors($bPreviousErrorMode);
    if (!$bLoaded) {
        fail('The installed application audit could not parse the sign-in form.');
    }

    $oXPath = new DOMXPath($oDocument);
    $oInputs = $oXPath->query(
        sprintf('//form[@id="%s"]//input[@type="hidden" and @name]', $sFormId)
    );
    if ($oInputs === false || $oInputs->length === 0) {
        fail('The installed application sign-in form has no hidden security fields.');
    }

    $aFields = [];
    foreach ($oInputs as $oInput) {
        if ($oInput instanceof DOMElement) {
            $aFields[$oInput->getAttribute('name')] = $oInput->getAttribute('value');
        }
    }

    return $aFields;
}

function performanceMetrics(string $sHtml): array
{
    if (preg_match(
        '/Queries time\s+[^|]*\|\s*(\d+)\s+Quer(?:y|ies)\s*\|\s*Generated in\s+([0-9]+(?:\.[0-9]+)?(?:ms|s))/i',
        $sHtml,
        $aMatches
    ) !== 1) {
        fail('The installed page did not expose development performance metrics.');
    }

    return ['queries' => (int)$aMatches[1], 'application_time' => $aMatches[2]];
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
            CURLOPT_TIMEOUT => 30,
            CURLOPT_COOKIEFILE => $sCookiePath,
            CURLOPT_COOKIEJAR => $sCookiePath,
            CURLOPT_USERAGENT => 'pH7Builder installed application audit'
        ]
    );
    if ($aFields !== null) {
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($aFields));
    }

    $sBody = curl_exec($oCurl);
    $iStatus = (int)curl_getinfo($oCurl, CURLINFO_RESPONSE_CODE);
    $fSeconds = (float)curl_getinfo($oCurl, CURLINFO_TOTAL_TIME);
    $sError = curl_error($oCurl);

    if (!is_string($sBody)) {
        fail('Installed application request failed: ' . $sError);
    }

    return ['status' => $iStatus, 'body' => $sBody, 'seconds' => $fSeconds];
}

function enableDevelopmentMode(string $sConfigPath, string $sConfig): void
{
    $sDevelopmentConfig = preg_replace(
        '/^environment\s*=\s*production\s*$/m',
        'environment = development',
        $sConfig,
        1,
        $iReplacementCount
    );
    if (!is_string($sDevelopmentConfig) || $iReplacementCount !== 1) {
        fail('The installed application was not in the expected production mode.');
    }
    if (file_put_contents($sConfigPath, $sDevelopmentConfig) === false) {
        fail('The installed application could not enter development mode for performance measurement.');
    }
}

function containsPhpFailure(string $sHtml): bool
{
    return preg_match('/(?:Fatal error|Parse error|Uncaught (?:Error|Exception))/i', $sHtml) === 1;
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
