<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/TweetSharing.php';

use PH7\TweetSharing;
use PHPUnit\Framework\TestCase;

class TweetSharingTest extends TestCase
{
    public function testGetMessage(): void
    {
        $sExpectedLink = 'https://twitter.com/intent/tweet?text=I+built+my+%23Social+%23DatingWebApp+with+%23pH7Builder+%F0%9F%98%8D%0A%23DatingSoftware+-%3E+%40pH7Soft+%3D%3E+https%3A%2F%2Fgithub.com%2FpH7Software%2FpH7-Social-Dating-CMS+%F0%9F%9A%80';
        $this->assertSame($sExpectedLink, TweetSharing::getMessage());
    }
}
