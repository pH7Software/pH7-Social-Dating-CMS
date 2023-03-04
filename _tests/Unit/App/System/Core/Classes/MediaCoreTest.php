<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/MediaCore.php';

use PH7\MediaCore;
use PHPUnit\Framework\TestCase;

final class MediaCoreTest extends TestCase
{
    public function testTitle(): void
    {
        // Title taken from my blog post https://01script.com/transformer-histoire-en-legende/
        $sTitle = '   Comment Transformer UNE IDÉE en LÉGENDE –  ';
        $sExpected = 'Comment Transformer UNE IDÉE en LÉGENDE –';
        $this->assertSame($sExpected, MediaCore::cleanTitle($sTitle));
    }
}
