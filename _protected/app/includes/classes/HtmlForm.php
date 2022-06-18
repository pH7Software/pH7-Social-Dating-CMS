<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

declare(strict_types=1);

namespace PH7;

trait HtmlForm
{
    private static function setCustomValidity(string $sMessage): array
    {
        return [
            'oninvalid' => sprintf(
                'this.setCustomValidity("%s")',
                $sMessage
            ),
            'oninput' => 'this.setCustomValidity(\'\')',
        ];
    }
}
