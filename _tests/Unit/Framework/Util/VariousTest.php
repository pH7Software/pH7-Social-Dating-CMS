<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Util
 */

namespace PH7\Test\Unit\Framework\Util;

use PH7\Framework\Util\Various;

class VariousTest extends \PHPUnit_Framework_TestCase
{
    public function testPaddingString()
    {
        $this->assertEquals('abc def ghiabc def ghiabc def ghiabc def', Various::padStr('abc def ghi'));
    }
 }
