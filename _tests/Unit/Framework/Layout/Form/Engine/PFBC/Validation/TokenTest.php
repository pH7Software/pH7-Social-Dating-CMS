<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Validation;

use PFBC\Validation\Token;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

final class TokenTest extends TestCase
{
    #[DataProvider('provideMalformedTokens')]
    public function testMalformedTokensFailValidationWithoutTypeErrors(mixed $mToken): void
    {
        self::assertFalse((new Token('malformed_token'))->isValid($mToken));
    }

    public static function provideMalformedTokens(): array
    {
        return [[null], [[]], [['unexpected']], [42], [false]];
    }
}
