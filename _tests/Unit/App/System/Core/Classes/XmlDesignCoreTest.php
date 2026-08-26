<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/design/XmlDesignCore.php';

use PH7\XmlDesignCore;
use PHPUnit\Framework\TestCase;

final class XmlDesignCoreTest extends TestCase
{
    public function testRssHeaderLinksDoNotContainDuplicates(): void
    {
        ob_start();
        XmlDesignCore::rssHeaderLinks();
        $sOutput = (string) ob_get_clean();

        preg_match_all('/<link\b[^>]*>/', $sOutput, $aMatches);

        $this->assertNotEmpty($aMatches[0]);
        $this->assertSame($aMatches[0], array_values(array_unique($aMatches[0])));
    }
}
