<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/design/VideoDesignCore.php';

use PH7\VideoDesignCore;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VideoDesignCoreTest extends TestCase
{
    public function testInvalidVideoProviderMessageIsEscaped(): void
    {
        $oVideo = new stdClass();
        $oVideo->duration = 0;
        $oVideo->title = 'Invalid video';
        $oVideo->file = 'https://"><img src=x onerror=alert(1)>.com/video';

        ob_start();
        VideoDesignCore::generate($oVideo);
        $sOutput = (string) ob_get_clean();

        $this->assertStringNotContainsString('<img src=x onerror=alert(1)>', $sOutput);
        $this->assertStringContainsString('&quot;&gt;', $sOutput);
    }
}
