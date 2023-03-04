<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Pagination;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    private const TOTAL_ITEMS = 80;
    private const CURRENT_PAGE = 0;

    public function testPaginationHtmlCode(): void
    {
        $oPagination = new Pagination(self::TOTAL_ITEMS, self::CURRENT_PAGE, Pagination::REQUEST_PARAM_NAME,);

        $sExpected = <<<HTML
<div class="clear"></div><nav class="center" role="navigation"><ul class="pagination"><li><a href="http://localhost:8888/?p=1">1</a></li><li><a href="http://localhost:8888/?p=2">2</a></li><li><a href="http://localhost:8888/?p=3">3</a></li><li class="next"><a href="http://localhost:8888/?p=1" aria-label="Next"><span aria-hidden="true">&rsaquo;</span></a></li><li><a href="http://localhost:8888/?p=80"><span aria-hidden="true">&raquo;</span></a></li></ul></nav>
HTML;
        $sActual = $oPagination->getHtmlCode();

        $this->assertSame($sExpected, $sActual);
    }
}
