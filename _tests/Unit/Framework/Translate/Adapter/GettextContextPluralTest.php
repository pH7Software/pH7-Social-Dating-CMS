<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Translate / Adapter
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Translate\Adapter;

use PHPUnit\Framework\TestCase;

final class GettextContextPluralTest extends TestCase
{
    protected function setUp(): void
    {
        require_once PH7_PATH_FRAMEWORK . 'Translate/Adapter/Gettext/gettext.inc.php';
        \_setlocale(LC_MESSAGES, 'ph7_TEST');
        \_textdomain('messages');

        foreach (['messages', 'test', 'test-category'] as $sDomain) {
            \_bindtextdomain($sDomain, sys_get_temp_dir());
            \_bind_textdomain_codeset($sDomain, 'UTF-8');
        }
    }

    public function testContextPluralUsesTheRequestedCount(): void
    {
        $this->assertSame('one profile', \_npgettext('members', 'one profile', 'many profiles', 1));
        $this->assertSame('many profiles', \_npgettext('members', 'one profile', 'many profiles', 2));
    }

    public function testDomainContextPluralUsesTheRequestedCount(): void
    {
        $this->assertSame('one message', \_dnpgettext('test', 'mail', 'one message', 'many messages', 1));
        $this->assertSame('many messages', \_dnpgettext('test', 'mail', 'one message', 'many messages', 2));
    }

    public function testDomainCategoryContextPluralUsesTheRequestedCount(): void
    {
        $this->assertSame(
            'one notification',
            \_dcnpgettext('test-category', 'alerts', 'one notification', 'many notifications', 1, LC_MESSAGES)
        );
        $this->assertSame(
            'many notifications',
            \_dcnpgettext('test-category', 'alerts', 'one notification', 'many notifications', 2, LC_MESSAGES)
        );
    }

    public function testPluralExpressionRejectsUnexpectedIdentifiers(): void
    {
        $oReader = new \gettext_reader(null);

        $sExpression = $oReader->sanitize_plural_expression('nplurals=2; plural=exit(1);');

        $this->assertStringNotContainsString('exit', $sExpression);
        $this->assertSame('nplurals=2;plural=n==1 ? (0) : (1);', $sExpression);
    }

    public function testInvalidPluralArithmeticFallsBackWithoutBreakingTheRequest(): void
    {
        $oReader = new \gettext_reader(null);
        $oReader->cache_translations = ['' => ''];
        $oReader->table_originals = [];
        $oReader->table_translations = [];
        $oReader->pluralheader = 'nplurals=2;plural=1/0;';

        $this->assertSame(0, $oReader->select_string(1));
        $this->assertSame(1, $oReader->select_string(2));
    }
}
