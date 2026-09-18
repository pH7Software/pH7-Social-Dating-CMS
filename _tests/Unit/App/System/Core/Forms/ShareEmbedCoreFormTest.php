<?php

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

use PHPUnit\Framework\TestCase;

final class ShareEmbedCoreFormTest extends TestCase
{
    public function testShareEmbedUsesHtml5VideoInsteadOfFlash(): void
    {
        $sSource = file_get_contents(
            PH7_PATH_SYS . 'core/forms/ShareEmbedCoreForm.php'
        );

        $this->assertIsString($sSource);
        $this->assertStringContainsString('<video src=', $sSource);
        $this->assertStringContainsString('escapeAttribute($sFileUrl)', $sSource);
        $this->assertStringNotContainsString('application/x-shockwave-flash', $sSource);
        $this->assertStringNotContainsString('<object', $sSource);
    }
}
