<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2021-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

declare(strict_types=1);

namespace PH7;

trait BulkAction
{
    /**
     * Determines if the action can be processed or not
     * by checking if the POST 'actions' has a correct value.
     *
     * @param array|string|null $mActions
     */
    protected function areActionsEligible($mActions): bool
    {
        return !empty($mActions) && is_array($mActions) && count($mActions) > 0;
    }
}
