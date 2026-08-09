<?php
/**
 * @license MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package PH7 / Test / Unit / Framework / Security / Validate
 */

namespace PH7\Test\Unit\Framework\Security\Validate;

use PH7\Framework\Security\Validate\Filter;
use PH7\Framework\Security\Validate\Purifier;
use PHPUnit\Framework\TestCase;

final class PurifierTest extends TestCase
{
    private Purifier $oPurifier;

    protected function setUp(): void
    {
        $this->oPurifier = new Purifier;
    }

    public function testReportedPayloadIsRemovedThroughLegacyFilterApi(): void
    {
        $sPayload = '<svg onload=window.__xss=String.fromCharCode(80,87,78,69,68)>';
        if (!defined('PH7_DOMAIN_COOKIE')) {
            define('PH7_DOMAIN_COOKIE', 'localhost');
        }

        $this->assertSame('', (new Filter)->xssClean($sPayload));
    }

    public function testPreservesSafeRichTextWithoutEventHandlers(): void
    {
        $sHtml = '<p><strong>Hello</strong> <a href="https://example.com" onclick="bad()">link</a></p>';

        $this->assertSame(
            '<p><strong>Hello</strong> <a href="https://example.com">link</a></p>',
            $this->oPurifier->xssClean($sHtml)
        );
    }

    public function testRejectsJavascriptUrls(): void
    {
        $this->assertSame(
            '<a>unsafe</a>',
            $this->oPurifier->xssClean('<a href="javascript:alert(1)">unsafe</a>')
        );
    }

    public function testRemovesInlineStylesAfterLegacyEditorRetirement(): void
    {
        $this->assertSame(
            '<p>Text</p>',
            $this->oPurifier->xssClean('<p style="color:red;position:absolute">Text</p>')
        );
    }

    public function testCleansNestedArrays(): void
    {
        $this->assertSame(
            ['safe' => '<em>Text</em>', 'nested' => ['unsafe' => '']],
            $this->oPurifier->xssClean([
                'safe' => '<em>Text</em>',
                'nested' => ['unsafe' => '<svg onload=alert(1)>']
            ])
        );
    }
}
