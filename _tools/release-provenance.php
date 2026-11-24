#!/usr/bin/env php
<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

const SPDX_VERSION = 'SPDX-2.3';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

[, $sVersion, $sArchivePath, $sOutputDirectory] = array_pad($argv, 4, '');
if (preg_match('/^\d{1,2}\.\d{1,2}\.\d{1,2}$/D', $sVersion) !== 1) {
    fail('Usage: php _tools/release-provenance.php VERSION ARCHIVE OUTPUT_DIRECTORY');
}

$sProjectRoot = dirname(__DIR__);
$sArchiveRealPath = realpath($sArchivePath) ?: '';
$sOutputRealPath = realpath($sOutputDirectory) ?: '';
if ($sArchiveRealPath === '' || !is_file($sArchiveRealPath) || $sOutputRealPath === '' || !is_dir($sOutputRealPath)) {
    fail('The archive and output directory must already exist.');
}

$sCommit = run(['git', '-C', $sProjectRoot, 'rev-parse', 'HEAD']);
$sCommitTimestamp = run(['git', '-C', $sProjectRoot, 'show', '-s', '--format=%cI', 'HEAD']);
$sCreated = gmdate(DATE_ATOM, (int)run(['git', '-C', $sProjectRoot, 'show', '-s', '--format=%ct', 'HEAD']));
$sArchiveChecksum = hash_file('sha256', $sArchiveRealPath);
if (!is_string($sArchiveChecksum)) {
    fail('Could not calculate the release archive checksum.');
}

$aLockFiles = [
    'runtime' => $sProjectRoot . '/composer.lock',
    'installer' => $sProjectRoot . '/_install/composer.lock'
];
$aPackages = [];
foreach ($aLockFiles as $sScope => $sLockFile) {
    $aLock = decodeJsonFile($sLockFile);
    foreach ($aLock['packages'] ?? [] as $aPackage) {
        if (!is_array($aPackage) || !isset($aPackage['name'], $aPackage['version'])) {
            continue;
        }

        $sPackageName = (string)$aPackage['name'];
        $sPackageVersion = ltrim((string)$aPackage['version'], 'v');
        $sPackageId = 'SPDXRef-' . preg_replace('/[^A-Za-z0-9.-]+/', '-', $sScope . '-' . $sPackageName);
        $aDeclaredLicences = isset($aPackage['license']) && is_array($aPackage['license'])
            ? array_values(array_filter($aPackage['license'], 'is_string'))
            : [];

        $aPackages[] = [
            'SPDXID' => $sPackageId,
            'name' => $sPackageName,
            'versionInfo' => $sPackageVersion,
            'downloadLocation' => 'NOASSERTION',
            'filesAnalyzed' => false,
            'licenseConcluded' => 'NOASSERTION',
            'licenseDeclared' => $aDeclaredLicences === [] ? 'NOASSERTION' : implode(' OR ', $aDeclaredLicences),
            'copyrightText' => 'NOASSERTION',
            'supplier' => 'NOASSERTION',
            'externalRefs' => [
                [
                    'referenceCategory' => 'PACKAGE-MANAGER',
                    'referenceType' => 'purl',
                    'referenceLocator' => sprintf('pkg:composer/%s@%s', $sPackageName, rawurlencode($sPackageVersion))
                ]
            ],
            'comment' => sprintf('Composer %s dependency.', $sScope)
        ];
    }
}

usort(
    $aPackages,
    static fn (array $aLeft, array $aRight): int => strcmp($aLeft['SPDXID'], $aRight['SPDXID'])
);
$aRelationships = [
    [
        'spdxElementId' => 'SPDXRef-DOCUMENT',
        'relationshipType' => 'DESCRIBES',
        'relatedSpdxElement' => 'SPDXRef-pH7Builder'
    ]
];
foreach ($aPackages as $aPackage) {
    $aRelationships[] = [
        'spdxElementId' => 'SPDXRef-pH7Builder',
        'relationshipType' => 'DEPENDS_ON',
        'relatedSpdxElement' => $aPackage['SPDXID']
    ];
}
array_unshift(
    $aPackages,
    [
        'SPDXID' => 'SPDXRef-pH7Builder',
        'name' => 'pH7Builder',
        'versionInfo' => $sVersion,
        'downloadLocation' => 'https://github.com/pH7Software/pH7-Social-Dating-CMS',
        'filesAnalyzed' => false,
        'licenseConcluded' => 'MIT',
        'licenseDeclared' => 'MIT',
        'copyrightText' => 'Copyright (c) 2012-2026 Pierre-Henry Soria and pH7Builder contributors.',
        'supplier' => 'Organization: pH7Software'
    ]
);

$aSbom = [
    'spdxVersion' => SPDX_VERSION,
    'dataLicense' => 'CC0-1.0',
    'SPDXID' => 'SPDXRef-DOCUMENT',
    'name' => 'pH7Builder-' . $sVersion,
    'documentNamespace' => sprintf('https://ph7builder.com/releases/%s/sbom/%s', $sVersion, $sCommit),
    'creationInfo' => [
        'created' => $sCreated,
        'creators' => ['Tool: pH7Builder release-provenance.php']
    ],
    'packages' => $aPackages,
    'relationships' => $aRelationships
];

$aBuildMetadata = [
    'schema_version' => 1,
    'product' => 'pH7Builder',
    'version' => $sVersion,
    'git_commit' => $sCommit,
    'git_commit_timestamp' => $sCommitTimestamp,
    'archive' => basename($sArchiveRealPath),
    'archive_sha256' => $sArchiveChecksum,
    'composer_lock_sha256' => hash_file('sha256', $aLockFiles['runtime']),
    'installer_composer_lock_sha256' => hash_file('sha256', $aLockFiles['installer']),
    'sbom' => sprintf('pH7Builder-v%s.spdx.json', $sVersion)
];

writeJson(
    $sOutputRealPath . sprintf('/pH7Builder-v%s.spdx.json', $sVersion),
    $aSbom
);
writeJson(
    $sOutputRealPath . sprintf('/pH7Builder-v%s.build.json', $sVersion),
    $aBuildMetadata
);

function decodeJsonFile(string $sPath): array
{
    $sContents = file_get_contents($sPath);
    if (!is_string($sContents)) {
        fail('Could not read ' . $sPath);
    }

    $aDecoded = json_decode($sContents, true, 512, JSON_THROW_ON_ERROR);

    return is_array($aDecoded) ? $aDecoded : [];
}

function writeJson(string $sPath, array $aContents): void
{
    $sJson = json_encode($aContents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    if (file_put_contents($sPath, $sJson . PHP_EOL, LOCK_EX) === false) {
        fail('Could not write ' . $sPath);
    }
}

function run(array $aCommand): string
{
    $aEscapedCommand = array_map('escapeshellarg', $aCommand);
    $aOutput = [];
    $iExitCode = 0;
    exec(implode(' ', $aEscapedCommand), $aOutput, $iExitCode);
    if ($iExitCode !== 0) {
        fail('Command failed: ' . implode(' ', $aCommand));
    }

    return trim(implode("\n", $aOutput));
}

function fail(string $sMessage): never
{
    fwrite(STDERR, $sMessage . PHP_EOL);
    exit(1);
}
