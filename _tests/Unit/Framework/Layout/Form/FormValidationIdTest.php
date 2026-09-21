<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * PFBC\Form::isValid() recovers the serialized form (and therefore its CSRF
 * token rule) from $_SESSION['pfbc'][$id]. Passing a request value as $id lets
 * the client choose which form's validation rules run against its data.
 *
 * Every call site must pass the form's own literal ID instead.
 */
class FormValidationIdTest extends TestCase
{
    private const SCANNED_DIRECTORIES = [
        'app',
        'framework'
    ];

    /**
     * Matches isValid() receiving anything other than a quoted literal,
     * e.g. isValid($_POST['submit_x']) or isValid($oHttpRequest->post('...')).
     */
    private const NON_LITERAL_ARGUMENT_PATTERN = '/::isValid\(\s*(?![\'"])[^)]/';

    public function testNoFormValidatesAgainstARequestSuppliedId(): void
    {
        $aOffendingFiles = [];

        foreach ($this->getPhpFiles() as $sFilePath) {
            $sContents = file_get_contents($sFilePath);

            if (preg_match(self::NON_LITERAL_ARGUMENT_PATTERN, $sContents)) {
                $aOffendingFiles[] = $this->getRelativePath($sFilePath);
            }
        }

        $this->assertSame(
            [],
            $aOffendingFiles,
            'PFBC\Form::isValid() must be given the form\'s literal ID. A request-supplied ID lets ' .
            'the client pick which validation rules (including the CSRF token check) are applied.'
        );
    }

    /**
     * Guards the invariant the fix relies on: the hidden submit element's value
     * is the form ID, so the literal passed to isValid() stays correct.
     */
    public function testHiddenSubmitValueMatchesTheFormId(): void
    {
        $aMismatches = [];

        foreach ($this->getPhpFiles() as $sFilePath) {
            $sContents = file_get_contents($sFilePath);

            if (!preg_match_all('/::isValid\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $sContents, $aValidated)) {
                continue;
            }

            preg_match_all('/new\s+\\\\?PFBC\\\\Form\(\s*[\'"]([^\'"]+)[\'"]/', $sContents, $aDeclared);
            $aDeclaredIds = array_unique($aDeclared[1]);

            if ($aDeclaredIds === []) {
                continue; // The form is built elsewhere; nothing to cross-check here.
            }

            foreach (array_unique($aValidated[1]) as $sValidatedId) {
                if (!in_array($sValidatedId, $aDeclaredIds, true)) {
                    $aMismatches[] = sprintf(
                        '%s validates "%s" but declares "%s"',
                        $this->getRelativePath($sFilePath),
                        $sValidatedId,
                        implode('", "', $aDeclaredIds)
                    );
                }
            }
        }

        $this->assertSame(
            [],
            $aMismatches,
            'The ID passed to PFBC\Form::isValid() must match the form actually built in that file, ' .
            'otherwise the wrong form is recovered from the session and its rules are skipped.'
        );
    }

    /**
     * @return iterable<string>
     */
    private function getPhpFiles(): iterable
    {
        foreach (self::SCANNED_DIRECTORIES as $sDirectory) {
            $sPath = PH7_PATH_PROTECTED . $sDirectory;

            if (!is_dir($sPath)) {
                continue;
            }

            /** @var SplFileInfo $oFile */
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sPath)) as $oFile) {
                if ($oFile->isFile() && $oFile->getExtension() === 'php') {
                    yield $oFile->getPathname();
                }
            }
        }
    }

    private function getRelativePath(string $sFilePath): string
    {
        return str_replace(PH7_PATH_PROTECTED, '', $sFilePath);
    }
}
