<?php

declare(strict_types=1);

namespace PH7\Tests\Unit\App\System\Core\Forms;

use PHPUnit\Framework\TestCase;

final class ShareEmbedCoreFormTest extends TestCase
{
    public function testShareEmbedUsesHtml5VideoInsteadOfFlash(): void
    {
        $sSource = file_get_contents(
            dirname(__DIR__, 6) . '/_protected/app/system/core/forms/ShareEmbedCoreForm.php'
        );

        $this->assertIsString($sSource);
        $this->assertStringContainsString('<video src=', $sSource);
        $this->assertStringContainsString('escapeAttribute($sFileUrl)', $sSource);
        $this->assertStringNotContainsString('application/x-shockwave-flash', $sSource);
        $this->assertStringNotContainsString('<object', $sSource);
    }
}
