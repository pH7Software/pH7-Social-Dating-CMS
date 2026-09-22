<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Affiliate
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Affiliate;

use PHPUnit\Framework\TestCase;

final class AffiliateLoginIncludeTest extends TestCase
{
    /**
     * Displaying the affiliate login form ends any member or admin session, and the affiliate
     * home and signup pages embed it. Opening the affiliate programme from the site signed
     * members and admins out, so they must get a link to the login page instead.
     */
    public function testEmbeddedLoginFormIsOnlyShownToVisitorsNotSignedInElsewhere(): void
    {
        $sTemplate = (string)file_get_contents(PH7_PATH_SYS_MOD . 'affiliate/views/base/tpl/login.inc.tpl');

        $sFormOnlyInTheElseBranch = '/\{if UserCore::auth\(\) OR AdminCore::auth\(\)\}'
            . '(?:(?!LoginForm::display).)*\{else\}.*LoginForm::display\(/s';

        $this->assertSame(1, substr_count($sTemplate, 'LoginForm::display('));
        $this->assertMatchesRegularExpression($sFormOnlyInTheElseBranch, $sTemplate);
    }
}
