<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Report;

require_once PH7_PATH_SYS_MOD . 'report/inc/class/Report.php';

use PH7\Report;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReportInputBoundaryTest extends TestCase
{
    #[DataProvider('validContentTypeProvider')]
    public function testSupportedContentTypeIsAccepted(string $sType): void
    {
        $this->assertTrue(Report::isValidContentType($sType));
    }

    #[DataProvider('unsafeContentTypeProvider')]
    public function testUnsupportedContentTypeIsRejected(mixed $mType): void
    {
        $this->assertFalse(Report::isValidContentType($mType));
    }

    public function testMailOutputUsesContextSpecificEscaping(): void
    {
        $sSource = file_get_contents(PH7_PATH_SYS_MOD . 'report/inc/class/Report.php');

        $this->assertIsString($sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($oUser->getProfileLink($sReporterUsername))', $sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($oUser->getProfileLink($sSpammerUsername))', $sSource);
        $this->assertStringContainsString('$sContentType = escape((string)$aData[\'type\'])', $sSource);
        $this->assertStringContainsString('$sReportUrl = escape((string)$aData[\'url\'])', $sSource);
        $this->assertStringContainsString('$sDescription = escape((string)$aData[\'desc\'])', $sSource);
    }

    public static function validContentTypeProvider(): iterable
    {
        foreach (Report::CONTENT_TYPES as $sType) {
            yield $sType => [$sType];
        }
    }

    public static function unsafeContentTypeProvider(): array
    {
        return [
            'empty' => [''],
            'case mismatch' => ['User'],
            'HTML payload' => ['<img src=x onerror=alert(1)>'],
            'unknown type' => ['payment'],
            'array' => [['user']]
        ];
    }
}
