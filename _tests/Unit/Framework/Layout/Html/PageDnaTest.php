<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Html
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Html;

use PH7\Framework\Layout\Html\PageDna;
use PHPUnit\Framework\TestCase;

final class PageDnaTest extends TestCase
{
    public function testCommentUsesConciseProjectAttribution(): void
    {
        $sComment = PageDna::generateHtmlComment();

        $this->assertStringContainsString('pH7Builder', $sComment);
        $this->assertStringContainsString('Pierre-Henry Soria', $sComment);
        $this->assertStringContainsString('github.com/pH7Software', $sComment);
        $this->assertStringNotContainsString('cannot be removed', $sComment);
        $this->assertStringNotContainsString('pH7CMS', $sComment);
    }

    public function testLegacyConstantNamesRemainCompatible(): void
    {
        $this->assertSame(PageDna::COMMENT_PH7BUILDER, PageDna::COMMENT_PH7CMS);
        $this->assertSame(PageDna::COMMENT_PH7BUILDER, PageDna::COMMENT_BUILT_WITH_PH7CMS);
    }
}
