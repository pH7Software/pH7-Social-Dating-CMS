<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Affiliate / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Affiliate\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'affiliate/inc/class/SocialSharing.php';

use PH7\SocialSharing;
use PHPUnit\Framework\TestCase;

class SocialSharingTest extends TestCase
{
    public function testGetMessage(): void
    {
        $sExpectedLink = 'https://twitter.com/intent/tweet?text=Let%27s+talk%21+%F0%9F%A4%97';
        $this->assertSame($sExpectedLink, SocialSharing::getTwitterLink('Let\'s talk! 🤗'));
    }
}
