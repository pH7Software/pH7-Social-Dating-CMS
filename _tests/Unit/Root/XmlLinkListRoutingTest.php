<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class XmlLinkListRoutingTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testHtmlLinkListsRenderLocalTemplatesWithoutSelfHttpRequests(): void
    {
        $sMainController = $this->readFile(
            '_protected/app/system/modules/xml/controllers/MainController.php'
        );
        self::assertStringContainsString('getLinksFromTemplate', $sMainController);
        self::assertStringContainsString('$this->view->display($sTemplate)', $sMainController);
        self::assertStringContainsString('$oXml->loadXML($sXml)', $sMainController);

        $aControllers = [
            '_protected/app/system/modules/xml/controllers/SitemapController.php' => 'links.xml.tpl',
            '_protected/app/system/modules/xml/controllers/RssController.php' => 'rss_links.xml.tpl'
        ];

        foreach ($aControllers as $sControllerPath => $sTemplate) {
            $sController = $this->readFile($sControllerPath);

            self::assertStringContainsString(
                sprintf("getLinksFromTemplate('%s')", $sTemplate),
                $sController,
                $sControllerPath
            );
            self::assertStringNotContainsString('new Link(', $sController, $sControllerPath);
            self::assertStringNotContainsString("Uri::get('xml'", $sController, $sControllerPath);
        }
    }

    private function readFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . '/' . $sRelativePath);

        self::assertIsString($sContents);

        return $sContents;
    }
}
