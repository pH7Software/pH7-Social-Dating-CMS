<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols -- Self-contained CLI entry point.

const REQUIRED_LOCALE_FILES = [
    'language.php',
    'config/config.ini',
    'LC_MESSAGES/global.po'
];

const CUSTOM_PLACEHOLDER_PATTERN = '/%(?:[0-9]+|[A-Za-z_][A-Za-z0-9_]*)%/u';
const MAX_REPORTED_FAILURES = 30;

[$bCoreOnly, $sTranslationsPath] = parse_options($argv);
$sProjectRoot = find_project_root(getcwd() ?: __DIR__);
$aFailures = [];
$aWarnings = [];
$aCatalogPaths = collect_catalogs($sProjectRoot . '/_protected/app/langs');

validate_locale_bundles($sProjectRoot . '/_protected/app/langs', false, $aFailures);

if (!$bCoreOnly) {
    $sTranslationsPath = $sTranslationsPath ?: dirname($sProjectRoot) . '/pH7-Internationalization';
    if (is_dir($sTranslationsPath)) {
        $aCatalogPaths = array_merge($aCatalogPaths, collect_catalogs($sTranslationsPath));
        validate_locale_bundles(
            $sTranslationsPath . '/_protected/app/langs',
            true,
            $aFailures
        );
    } else {
        $aWarnings[] = sprintf(
            'Translation checkout not found at %s; use --translations=/path or --core-only.',
            $sTranslationsPath
        );
    }
}

$aCatalogPaths = array_values(array_unique($aCatalogPaths));
sort($aCatalogPaths);
$sMsgfmtPath = find_executable('msgfmt');
$iEntries = 0;

foreach ($aCatalogPaths as $sCatalogPath) {
    $aResult = parse_catalog($sCatalogPath);
    $iEntries += $aResult['entry_count'];

    foreach ($aResult['failures'] as $sFailure) {
        $aFailures[] = $sCatalogPath . ': ' . $sFailure;
    }
    foreach ($aResult['warnings'] as $sWarning) {
        $aWarnings[] = $sCatalogPath . ': ' . $sWarning;
    }

    if ($sMsgfmtPath !== null) {
        validate_with_msgfmt($sMsgfmtPath, $sCatalogPath, $aFailures);
    }
}

if ($sMsgfmtPath === null) {
    $aWarnings[] = 'GNU Gettext msgfmt is unavailable; native format validation was skipped.';
}

foreach ($aWarnings as $sWarning) {
    fwrite(STDERR, "WARNING: {$sWarning}\n");
}
foreach (array_slice($aFailures, 0, MAX_REPORTED_FAILURES) as $sFailure) {
    fwrite(STDERR, "ERROR: {$sFailure}\n");
}
if (count($aFailures) > MAX_REPORTED_FAILURES) {
    $iOmittedFailures = count($aFailures) - MAX_REPORTED_FAILURES;
    fwrite(
        STDERR,
        sprintf("ERROR: %d additional error(s) omitted; fix the reported errors and rerun.\n", $iOmittedFailures)
    );
}

printf(
    "Audited %d catalogue(s), %d message entries, %d warning(s), %d error(s).\n",
    count($aCatalogPaths),
    $iEntries,
    count($aWarnings),
    count($aFailures)
);

exit($aFailures === [] ? 0 : 1);

/**
 * @return array{bool, string|null}
 */
function parse_options(array $aArguments): array
{
    $bCoreOnly = false;
    $sTranslationsPath = null;

    foreach (array_slice($aArguments, 1) as $sArgument) {
        if ($sArgument === '--core-only') {
            $bCoreOnly = true;
        } elseif (str_starts_with($sArgument, '--translations=')) {
            $sTranslationsPath = substr($sArgument, strlen('--translations='));
            if ($sTranslationsPath === '') {
                fail_usage('--translations requires a path.');
            }
        } else {
            fail_usage('Unknown option: ' . $sArgument);
        }
    }

    if ($bCoreOnly && $sTranslationsPath !== null) {
        fail_usage('--core-only and --translations cannot be used together.');
    }

    return [$bCoreOnly, $sTranslationsPath];
}

function fail_usage(string $sMessage): never
{
    fwrite(STDERR, $sMessage . "\n");
    fwrite(STDERR, "Usage: php audit.php [--core-only | --translations=/absolute/path]\n");
    exit(2);
}

function find_project_root(string $sStartPath): string
{
    $sPath = realpath($sStartPath);
    while ($sPath !== false && $sPath !== dirname($sPath)) {
        if (is_file($sPath . '/composer.json') && is_dir($sPath . '/_protected')) {
            return $sPath;
        }
        $sPath = dirname($sPath);
    }

    throw new RuntimeException('Run this audit from the pH7Builder repository.');
}

/**
 * @return list<string>
 */
function collect_catalogs(string $sRootPath): array
{
    if (!is_dir($sRootPath)) {
        return [];
    }

    $aCatalogPaths = [];
    $oIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sRootPath, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($oIterator as $oFile) {
        if ($oFile->isFile() && in_array(strtolower($oFile->getExtension()), ['po', 'pot'], true)) {
            $aCatalogPaths[] = $oFile->getPathname();
        }
    }

    return $aCatalogPaths;
}

function validate_locale_bundles(string $sLangRoot, bool $bRequireMo, array &$aFailures): void
{
    if (!is_dir($sLangRoot)) {
        return;
    }

    foreach (glob($sLangRoot . '/*', GLOB_ONLYDIR) ?: [] as $sLocalePath) {
        $sLocaleName = basename($sLocalePath);
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $sLocaleName)) {
            $aFailures[] = sprintf('%s is not a canonical xx_YY locale directory.', $sLocalePath);
        }

        foreach (REQUIRED_LOCALE_FILES as $sRelativePath) {
            if (!is_file($sLocalePath . '/' . $sRelativePath)) {
                $aFailures[] = sprintf('%s is missing %s.', $sLocalePath, $sRelativePath);
            }
        }

        if ($bRequireMo && !is_file($sLocalePath . '/LC_MESSAGES/global.mo')) {
            $aFailures[] = sprintf('%s is missing LC_MESSAGES/global.mo.', $sLocalePath);
        }

        validate_language_php($sLocalePath . '/language.php', $aFailures);
        validate_locale_config($sLocalePath . '/config/config.ini', $sLocaleName, $aFailures);
    }
}

function validate_language_php(string $sPath, array &$aFailures): void
{
    if (!is_file($sPath)) {
        return;
    }

    $aResult = run_command([PHP_BINARY, '-l', $sPath]);
    if ($aResult['exit_code'] !== 0) {
        $aFailures[] = sprintf('%s failed PHP syntax validation: %s', $sPath, $aResult['output']);
    }
}

function validate_locale_config(string $sPath, string $sLocaleName, array &$aFailures): void
{
    if (!is_file($sPath)) {
        return;
    }

    $aConfig = @parse_ini_file($sPath, true, INI_SCANNER_RAW);
    if (!is_array($aConfig)) {
        $aFailures[] = $sPath . ' is not valid INI.';
        return;
    }

    $aLanguage = $aConfig['language'] ?? [];
    $sLang = trim((string)($aLanguage['lang'] ?? ''), " \t\n\r\0\x0B\"");
    $sDirection = strtolower(trim((string)($aLanguage['dir'] ?? '')));
    $sCharset = strtolower(trim((string)($aLanguage['charset'] ?? ''), " \t\n\r\0\x0B\""));

    if (!preg_match('/^[a-z]{2}(?:-[A-Z]{2})?$/', $sLang)) {
        $aFailures[] = sprintf('%s has invalid language.lang value %s.', $sPath, var_export($sLang, true));
    } elseif (substr($sLocaleName, 0, 2) !== substr($sLang, 0, 2)) {
        $aFailures[] = sprintf('%s language.lang does not match %s.', $sPath, $sLocaleName);
    }

    if (!in_array($sDirection, ['ltr', 'rtl'], true)) {
        $aFailures[] = sprintf('%s language.dir must be ltr or rtl.', $sPath);
    }
    if (!in_array($sCharset, ['utf-8', 'utf8'], true)) {
        $aFailures[] = sprintf('%s language.charset must be UTF-8.', $sPath);
    }

    $sTimezone = trim((string)($aConfig['language.application']['timezone'] ?? ''), " \t\n\r\0\x0B\"");
    if ($sTimezone === '' || !in_array($sTimezone, timezone_identifiers_list(), true)) {
        $aFailures[] = sprintf('%s has an invalid application timezone.', $sPath);
    }
}

/**
 * @return array{entry_count: int, failures: list<string>, warnings: list<string>}
 */
function parse_catalog(string $sPath): array
{
    $sContents = file_get_contents($sPath);
    if ($sContents === false) {
        return ['entry_count' => 0, 'failures' => ['could not be read.'], 'warnings' => []];
    }

    $aFailures = [];
    if (!mb_check_encoding($sContents, 'UTF-8')) {
        $aFailures[] = 'is not valid UTF-8.';
    }

    $aEntries = [];
    $aEntry = new_catalog_entry();
    $sCurrentField = '';
    $iCurrentIndex = 0;
    $bObsolete = false;

    foreach (preg_split('/\r\n|\n|\r/', $sContents) ?: [] as $iLine => $sLine) {
        $iLineNumber = $iLine + 1;
        if (str_starts_with($sLine, '#~')) {
            $bObsolete = true;
            continue;
        }
        if (trim($sLine) === '') {
            append_catalog_entry($aEntries, $aEntry, $bObsolete);
            $aEntry = new_catalog_entry();
            $sCurrentField = '';
            $iCurrentIndex = 0;
            $bObsolete = false;
            continue;
        }
        if (str_starts_with($sLine, '#,')) {
            $aEntry['fuzzy'] = $aEntry['fuzzy'] || str_contains($sLine, 'fuzzy');
            continue;
        }
        if (str_starts_with($sLine, '#')) {
            continue;
        }

        if (preg_match('/^(msgctxt|msgid_plural|msgid|msgstr(?:\[([0-9]+)\])?)\s+(".*")$/', $sLine, $aMatch)) {
            $sLabel = $aMatch[1];
            $iCurrentIndex = isset($aMatch[2]) && $aMatch[2] !== '' ? (int)$aMatch[2] : 0;
            $sCurrentField = str_starts_with($sLabel, 'msgstr') ? 'msgstr' : $sLabel;
            $sValue = decode_po_string($aMatch[3], $iLineNumber, $aFailures);
            if ($sCurrentField === 'msgstr') {
                $aEntry['msgstr'][$iCurrentIndex] = $sValue;
            } else {
                $aEntry[$sCurrentField] = $sValue;
            }
            $aEntry['line'] = $aEntry['line'] ?: $iLineNumber;
            continue;
        }

        if (preg_match('/^\s*(".*")$/', $sLine, $aMatch) && $sCurrentField !== '') {
            $sValue = decode_po_string($aMatch[1], $iLineNumber, $aFailures);
            if ($sCurrentField === 'msgstr') {
                $aEntry['msgstr'][$iCurrentIndex] = ($aEntry['msgstr'][$iCurrentIndex] ?? '') . $sValue;
            } else {
                $aEntry[$sCurrentField] .= $sValue;
            }
            continue;
        }

        $aFailures[] = sprintf('line %d has unrecognised PO syntax.', $iLineNumber);
    }

    append_catalog_entry($aEntries, $aEntry, $bObsolete);

    $aWarnings = [];
    $aMessageKeys = [];
    $iUntranslated = 0;
    $iFuzzy = 0;
    $bIsTemplate = strtolower(pathinfo($sPath, PATHINFO_EXTENSION)) === 'pot';
    $bIsSourceLocale = str_contains(str_replace('\\', '/', $sPath), '/en_US/');

    foreach ($aEntries as $aParsedEntry) {
        if ($aParsedEntry['msgid'] === '') {
            validate_catalog_header($aParsedEntry, $aFailures);
            continue;
        }

        $sKey = $aParsedEntry['msgctxt'] . "\x04" . $aParsedEntry['msgid'];
        if (isset($aMessageKeys[$sKey])) {
            $aFailures[] = sprintf(
                'line %d duplicates the message first defined on line %d.',
                $aParsedEntry['line'],
                $aMessageKeys[$sKey]
            );
        } else {
            $aMessageKeys[$sKey] = $aParsedEntry['line'];
        }

        $iFuzzy += $aParsedEntry['fuzzy'] ? 1 : 0;
        if (!$bIsTemplate && !$bIsSourceLocale && translations_are_empty($aParsedEntry['msgstr'])) {
            $iUntranslated++;
        }
        validate_placeholders($aParsedEntry, $aFailures);
    }

    if ($iUntranslated > 0) {
        $aWarnings[] = sprintf('%d message(s) are untranslated.', $iUntranslated);
    }
    if ($iFuzzy > 0) {
        $aWarnings[] = sprintf('%d message(s) are fuzzy.', $iFuzzy);
    }

    return [
        'entry_count' => count($aEntries),
        'failures' => $aFailures,
        'warnings' => $aWarnings
    ];
}

function new_catalog_entry(): array
{
    return [
        'msgctxt' => '',
        'msgid' => '',
        'msgid_plural' => '',
        'msgstr' => [],
        'fuzzy' => false,
        'line' => 0
    ];
}

function append_catalog_entry(array &$aEntries, array $aEntry, bool $bObsolete): void
{
    if (!$bObsolete && $aEntry['line'] !== 0) {
        $aEntries[] = $aEntry;
    }
}

function decode_po_string(string $sLiteral, int $iLine, array &$aFailures): string
{
    if (strlen($sLiteral) < 2 || $sLiteral[0] !== '"' || $sLiteral[-1] !== '"') {
        $aFailures[] = sprintf('line %d has an invalid quoted PO string.', $iLine);
        return '';
    }

    return stripcslashes(substr($sLiteral, 1, -1));
}

function validate_catalog_header(array $aEntry, array &$aFailures): void
{
    $sHeader = $aEntry['msgstr'][0] ?? '';
    if (!preg_match('/^Content-Type:.*charset=UTF-8/im', $sHeader)) {
        $aFailures[] = 'catalogue header must declare UTF-8.';
    }
}

function translations_are_empty(array $aTranslations): bool
{
    foreach ($aTranslations as $sTranslation) {
        if ($sTranslation !== '') {
            return false;
        }
    }

    return true;
}

function validate_placeholders(array $aEntry, array &$aFailures): void
{
    foreach ($aEntry['msgstr'] as $iIndex => $sTranslation) {
        if ($sTranslation === '') {
            continue;
        }

        $sSource = $iIndex === 0 || $aEntry['msgid_plural'] === ''
            ? $aEntry['msgid']
            : $aEntry['msgid_plural'];

        if (placeholder_counts($sSource) !== placeholder_counts($sTranslation)) {
            $aFailures[] = sprintf(
                'line %d changes custom placeholders in msgstr[%d].',
                $aEntry['line'],
                $iIndex
            );
        }
    }
}

function placeholder_counts(string $sText): array
{
    preg_match_all(CUSTOM_PLACEHOLDER_PATTERN, $sText, $aMatches);
    $aCounts = array_count_values($aMatches[0]);
    ksort($aCounts);

    return $aCounts;
}

function find_executable(string $sName): ?string
{
    $aExtensions = PHP_OS_FAMILY === 'Windows' ? ['', '.exe', '.bat', '.cmd'] : [''];
    foreach (explode(PATH_SEPARATOR, getenv('PATH') ?: '') as $sDirectory) {
        foreach ($aExtensions as $sExtension) {
            $sCandidate = rtrim($sDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sName . $sExtension;
            if (is_file($sCandidate) && is_executable($sCandidate)) {
                return $sCandidate;
            }
        }
    }

    return null;
}

function validate_with_msgfmt(string $sMsgfmtPath, string $sCatalogPath, array &$aFailures): void
{
    $sOutputPath = tempnam(sys_get_temp_dir(), 'ph7-msgfmt-');
    if ($sOutputPath === false) {
        $aFailures[] = 'Could not allocate a temporary file for msgfmt.';
        return;
    }

    try {
        $aResult = run_command([
            $sMsgfmtPath,
            '--check',
            '--check-format',
            '-o',
            $sOutputPath,
            $sCatalogPath
        ]);
        if ($aResult['exit_code'] !== 0) {
            $aFailures[] = sprintf('%s failed msgfmt validation: %s', $sCatalogPath, $aResult['output']);
        }
    } finally {
        @unlink($sOutputPath);
    }
}

function run_command(array $aCommand): array
{
    $rProcess = proc_open($aCommand, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $aPipes);
    if (!is_resource($rProcess)) {
        return ['exit_code' => 1, 'output' => 'Could not start process.'];
    }

    $sOutput = stream_get_contents($aPipes[1]) ?: '';
    $sError = stream_get_contents($aPipes[2]) ?: '';
    fclose($aPipes[1]);
    fclose($aPipes[2]);

    return [
        'exit_code' => proc_close($rProcess),
        'output' => trim($sOutput . "\n" . $sError)
    ];
}
