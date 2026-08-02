<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Payment / Gateway / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Payment\Gateway\Api;

use PH7\Framework\Payment\Gateway\Api\Provider;
use PHPUnit\Framework\TestCase;

final class ProviderTest extends TestCase
{
    public function testGenerateEscapesHiddenFieldAttributes(): void
    {
        $oProvider = new class extends Provider {
        };
        $oProvider->param('reference', '"><script>alert(1)</script>&');

        $sHtml = $oProvider->generate();

        $this->assertStringContainsString(
            'value="&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;&amp;"',
            $sHtml
        );
        $this->assertStringNotContainsString('<script>', $sHtml);
    }
}
