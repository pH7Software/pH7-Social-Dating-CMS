<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Util
 */

namespace PH7\Test\Unit\Framework\Util;

use PH7\Framework\Registry\Registry;
use PH7\Framework\Translate\Lang;

class LangTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        new Lang; // Load "Lang" class
        Registry::getInstance()->lang = [];
    }

    public function testTranslate()
    {
        $sName = 'Pierre-Henry';
        $this->assertSame('Hello Pierre-Henry', t('Hello %0%', $sName));
    }

    public function testIsoCode()
    {
        $sLocaleName = 'en_US';
        $sLangCode = Lang::getIsoCode($sLocaleName);

        $this->assertSame('en', $sLangCode);
    }
 }
