<?php

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Compress;

use PH7\Framework\Compress\Minify\Js;
use PHPUnit\Framework\TestCase;

final class CompressAutoloadTest extends TestCase
{
    public function testJavaScriptMinifierUsesFilesystemClassCase(): void
    {
        $sCompress = file_get_contents(PH7_PATH_FRAMEWORK . 'Compress/Compress.class.php');

        $this->assertIsString($sCompress);
        $this->assertStringContainsString('Minify\\Js::minify(', $sCompress);
        $this->assertTrue(class_exists(Js::class));
        $this->assertNotEmpty(Js::minify("var answer = 42;\n"));
    }
}
