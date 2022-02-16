<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
