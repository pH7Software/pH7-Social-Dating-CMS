<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

interface NudityDetectable
{
    public function isNudityFilterEligible(): bool;

    /**
     * Overwrite $iApproved if the image doesn't seem suitable to anyone.
     */
    public function checkNudityFilter(): void;
}
