<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Contact;

use PHPUnit\Framework\TestCase;

final class ContactMailBoundaryTest extends TestCase
{
    public function testVisitorDataUsesTheCorrectMailHtmlOutputContext(): void
    {
        $sSource = file_get_contents(PH7_PATH_SYS_MOD . 'contact/inc/class/Contact.php');

        $this->assertIsString($sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($this->sMail)', $sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($this->sPhone)', $sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($this->sUrl)', $sSource);
        $this->assertStringContainsString("preg_match('~^https?://~i', \$this->sUrl) !== 1", $sSource);
        $this->assertStringContainsString('escape((string)$this->browser->getUserAgent())', $sSource);
        $this->assertStringContainsString('$oStr->escapeAttribute($this->httpRequest->currentUrl())', $sSource);
    }
}
