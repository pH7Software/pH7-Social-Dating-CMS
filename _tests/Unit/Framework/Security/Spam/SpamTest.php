<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Spam
 */

namespace PH7\Test\Unit\Framework\Security\Spam;

use PH7\Framework\Security\Spam\Spam;
use PHPUnit_Framework_TestCase;

class SpamTest extends PHPUnit_Framework_TestCase
{
    public function testNotDuplicatedTest()
    {
        $sText1 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes...';
        $sText2 = 'Hi, so I am on the sofa watching TV for hours. TV shows are soooo gooood and soooo stupids.... sometimes?';

        $this->assertFalse(Spam::detectDuplicate($sText1, $sText2));
    }

    public function testDuplicatedTest()
    {
        $sText1 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes...';
        $sText2 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes!!!'; // Almost the same, so it's a duplicate!

        $this->assertTrue(Spam::detectDuplicate($sText1, $sText2));
    }
}
