<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC / Element
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Element;

// PFBC registers its own spl_autoload_register in Form.class.php
require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Element\Token;
use PFBC\Form;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    public function testTokenAlwaysRendersAHiddenField(): void
    {
        new Form('csrf_token_test');
        $oToken = new Token('csrf_token_test');

        ob_start();
        $oToken->render();
        $sHtml = ob_get_clean();

        $this->assertStringContainsString('name="security_token"', $sHtml);
        $this->assertMatchesRegularExpression('/value="[^"]+"/', $sHtml);
    }
}
