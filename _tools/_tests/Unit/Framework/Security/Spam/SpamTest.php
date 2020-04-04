<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Spam
 */

namespace PH7\Test\Unit\Framework\Security\Spam;

use PH7\Framework\Security\Spam\Spam;
use PHPUnit_Framework_TestCase;

class SpamTest extends PHPUnit_Framework_TestCase
{
    public function testNotDuplicatedText()
    {
        $sText1 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes...';
        $sText2 = 'Hi, so I am on the sofa watching TV for hours. TV shows are soooo gooood and soooo stupids.... sometimes?';

        $this->assertFalse(Spam::detectDuplicate($sText1, $sText2));
    }

    public function testDuplicatedText()
    {
        $sText1 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes...';
        $sText2 = 'Hi, so I am writing a test for a while and I love that, because tests are important, and yes!!!'; // Almost the same, so it's a duplicate!

        $this->assertTrue(Spam::detectDuplicate($sText1, $sText2));
    }

    public function testTooManyLinks()
    {
        $sText = 'Hi, can you go to http://affiliate-link.com this website http://affiliate-link.com/?ref=4933 is so good!';

        $this->assertTrue(Spam::areUrls($sText, 1));
    }

    public function testCannotHaveUrls()
    {
        $sText = 'My URL is http://ph7.me';

        $this->assertTrue(Spam::areUrls($sText, 0));
    }

    public function testCorrectAmountOfLinks()
    {
        $sText = 'I am nice. I just share one great website because I think it is a great resource: https://wikipedia.com ;)';

        $this->assertFalse(Spam::areUrls($sText, 1));
    }

    public function testTooManyEmails()
    {
        $sText = 'Hi! Please email me eva.nice@mail.com and even at evalana@mymail.io!';

        $this->assertTrue(Spam::areEmails($sText, 1));
    }

    public function testCannotHaveEmails()
    {
        $sText = 'Hi! Please email me eva.nice@mail.com!';

        $this->assertTrue(Spam::areEmails($sText, 0));
    }

    public function testCorrectAmountOfEmails()
    {
        $sText = 'Hi! Please email me eva.nice@mail.com!';

        $this->assertFalse(Spam::areEmails($sText, 1));
    }
}
