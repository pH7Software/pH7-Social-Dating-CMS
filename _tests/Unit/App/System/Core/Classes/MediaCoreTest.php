<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/MediaCore.php';

use PH7\MediaCore;
use PHPUnit_Framework_TestCase;

class MediaCoreTest extends PHPUnit_Framework_TestCase
{
    public function testTitle()
    {
        // Title taken from my blog post https://01script.com/transformer-histoire-en-legende/
        $sTitle = '   Comment Transformer UNE IDÉE en LÉGENDE –  ';
        $sExpected = 'Comment Transformer UNE IDÉE en LÉGENDE –';
        $this->assertSame($sExpected, MediaCore::cleanTitle($sTitle));
    }
}
