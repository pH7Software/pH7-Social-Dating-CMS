<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Page;
use PH7\Framework\Navigation\Pagination;
use PHPUnit\Framework\TestCase;

final class PageTest extends TestCase
{
    private Page $oPage;

    private const TOTAL_ITEMS = 80;
    private const MAX_ITEMS_PER_PAGE = 12;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oPage = new Page();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($_GET);
    }

    public function testAmountPagesOnDefaultPage(): void
    {
        $this->oPage->getTotalPages(self::TOTAL_ITEMS, self::MAX_ITEMS_PER_PAGE);

        $this->assertSame(1, $this->oPage->getCurrentPage());
        $this->assertSame(0, $this->oPage->getFirstItem());
        $this->assertSame(12, $this->oPage->getNbItemsPerPage());
    }

    public function testAmountPagesOnSpecifiedPage(): void
    {
        $_GET[Pagination::REQUEST_PARAM_NAME] = 4;

        $this->oPage->getTotalPages(self::TOTAL_ITEMS, self::MAX_ITEMS_PER_PAGE);

        $this->assertSame(4, $this->oPage->getCurrentPage());
        $this->assertSame(36, $this->oPage->getFirstItem());
        $this->assertSame(12, $this->oPage->getNbItemsPerPage());
    }

    public function testAmountPagesOnFewItems(): void
    {
        $iTotalItems = 10;
        $iMaxItemsPerPage = 15;

        $this->oPage->getTotalPages($iTotalItems, $iMaxItemsPerPage);

        $this->assertSame(1, $this->oPage->getCurrentPage());
        $this->assertSame(0, $this->oPage->getFirstItem());
        $this->assertSame(15, $this->oPage->getNbItemsPerPage());
    }
}
