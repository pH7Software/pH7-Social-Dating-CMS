<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\User\Controller;

use PHPUnit\Framework\TestCase;

final class BrowseControllerTest extends TestCase
{
    public function testOnlyEmptySearchResultsTriggerTheNoResultsRedirect(): void
    {
        $sController = file_get_contents(
            dirname(__DIR__, 7) . '/_protected/app/system/modules/user/controllers/BrowseController.php'
        );

        $this->assertIsString($sController);
        $this->assertStringContainsString('$this->isSearch() && empty($aUsers)', $sController);
        $this->assertStringNotContainsString('$this->isSearch() && !empty($aUsers)', $sController);
    }
}
