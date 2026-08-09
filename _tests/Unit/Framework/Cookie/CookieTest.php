<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Cookie
 */

declare(strict_types=1);

namespace PH7\Framework\Cookie;

function setcookie(string $sName, string $sValue = '', array|int $mOptions = 0): bool
{
    $GLOBALS['ph7_cookie_test_calls'][] = [
        'name' => $sName,
        'value' => $sValue,
        'options' => $mOptions
    ];

    return true;
}

namespace PH7\Test\Unit\Framework\Cookie;

use PH7\Framework\Cookie\Cookie;
use PHPUnit\Framework\TestCase;

final class CookieTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['ph7_cookie_test_calls'] = [];
        putenv('PH7_COOKIE_DOMAIN');
    }

    public function testArrayCookiesUseOneRelativeLifetime(): void
    {
        $iBefore = time();

        (new Cookie())->set(
            [
                'remember_hash' => 'hash-value',
                'remember_member_id' => 42
            ],
            null,
            90 * 24 * 60 * 60
        );

        $aCalls = $GLOBALS['ph7_cookie_test_calls'];
        $this->assertCount(2, $aCalls);
        $this->assertSame(
            $aCalls[0]['options']['expires'],
            $aCalls[1]['options']['expires']
        );
        $this->assertGreaterThanOrEqual($iBefore + 90 * 24 * 60 * 60, $aCalls[0]['options']['expires']);
        $this->assertLessThanOrEqual(time() + 90 * 24 * 60 * 60, $aCalls[0]['options']['expires']);
        $this->assertSame('42', $aCalls[1]['value']);
    }

    public function testMissingCookieValueIsWrittenAsAnEmptyString(): void
    {
        (new Cookie())->set('empty_cookie');

        $this->assertSame('', $GLOBALS['ph7_cookie_test_calls'][0]['value']);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['ph7_cookie_test_calls']);
        putenv('PH7_COOKIE_DOMAIN');

        parent::tearDown();
    }
}
