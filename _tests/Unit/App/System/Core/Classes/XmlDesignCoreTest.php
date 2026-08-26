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

use PH7\NewsFeedCore;
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

    public function testSoftwareNewsRejectsUnsafeLinksAndEscapesRemoteText(): void
    {
        $oNewsFeed = new class extends NewsFeedCore {
            public function getSoftware($iNum = self::DEFAULT_NUMBER_ITEMS): array
            {
                return [
                    'unsafe' => [
                        'link' => 'javascript://alert(1)',
                        'title' => 'Unsafe title',
                        'description' => 'Unsafe description'
                    ],
                    'safe' => [
                        'link' => 'https://ph7builder.com/news?a=1&b=2',
                        'title' => '<strong>Trusted & title</strong>',
                        'description' => '<p onclick="evil()">Safe <b>summary</b> &amp; details. Let&#8217;s go.</p>'
                    ]
                ];
            }
        };

        ob_start();
        XmlDesignCore::softwareNews(2, $oNewsFeed);
        $sOutput = (string)ob_get_clean();

        $this->assertStringNotContainsString('javascript:', $sOutput);
        $this->assertStringNotContainsString('Unsafe title', $sOutput);
        $this->assertStringNotContainsString('onclick', $sOutput);
        $this->assertSame(1, substr_count($sOutput, '<h4>'));
        $this->assertStringContainsString(
            'href="https://ph7builder.com/news?a=1&amp;b=2"',
            $sOutput
        );
        $this->assertStringContainsString('rel="noopener noreferrer"', $sOutput);
        $this->assertStringContainsString('Trusted &amp; title', $sOutput);
        $this->assertStringContainsString('Safe summary &amp; details. Let’s go.', $sOutput);
        $this->assertStringNotContainsString('&amp;#8217;', $sOutput);
    }

    public function testSoftwareNewsKeepsRemoteDescriptionsConcise(): void
    {
        $oNewsFeed = new class extends NewsFeedCore {
            public function getSoftware($iNum = self::DEFAULT_NUMBER_ITEMS): array
            {
                return [
                    'safe' => [
                        'link' => 'https://ph7builder.com/news',
                        'title' => 'Project news',
                        'description' => '<p>' . str_repeat('A', 300) . '</p>'
                    ]
                ];
            }
        };

        ob_start();
        XmlDesignCore::softwareNews(1, $oNewsFeed);
        $sOutput = (string)ob_get_clean();

        $this->assertStringContainsString(str_repeat('A', 240) . '...', $sOutput);
        $this->assertStringNotContainsString(str_repeat('A', 241), $sOutput);
    }
}
