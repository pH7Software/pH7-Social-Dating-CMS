<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Affiliate / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Affiliate\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'affiliate/inc/class/SocialSharing.php';

use PH7\SocialSharing;
use PHPUnit_Framework_TestCase;

class SocialSharingTest extends PHPUnit_Framework_TestCase
{
    public function testGetMessage()
    {
        $sExpectedLink = 'https://twitter.com/intent/tweet?text=Let%27s+talk%21+%F0%9F%A4%97';
        $this->assertSame($sExpectedLink, SocialSharing::getTwitterLink('Let\'s talk! 🤗'));
    }
}
