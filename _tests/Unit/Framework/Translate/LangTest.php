<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Util
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Util;

use PH7\Framework\Registry\Registry;
use PH7\Framework\Translate\Lang;
use PHPUnit\Framework\TestCase;

final class LangTest extends TestCase
{
    protected function setUp(): void
    {
        new Lang; // Load "Lang" class
        Registry::getInstance()->lang = [];
    }

    public function testTranslate(): void
    {
        $sName = 'Pierre-Henry';
        $this->assertSame('Hello Pierre-Henry', t('Hello %0%', $sName));
    }

    public function testIsoCodeWithDefaultIsoCodePosition(): void
    {
        $sLocaleName = 'nl_NL';
        $sLangCode = Lang::getIsoCode($sLocaleName);

        $this->assertSame('nl', $sLangCode);
    }

    public function testIsoCodeWithFirstIsoCode(): void
    {
        $sLocaleName = 'en_US';
        $sLangCode = Lang::getIsoCode($sLocaleName, Lang::FIRST_ISO_CODE);

        $this->assertSame('en', $sLangCode);
    }

    public function testIsoCodeWithLastIsoCode(): void
    {
        $sLocaleName = 'en_US';
        $sLangCode = Lang::getIsoCode($sLocaleName, Lang::LAST_ISO_CODE);

        $this->assertSame('us', $sLangCode);
    }
}
