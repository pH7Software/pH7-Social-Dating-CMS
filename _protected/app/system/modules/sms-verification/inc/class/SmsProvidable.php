<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

interface SmsProvidable
{
    public function send(string $sPhoneNumber, string $sTextMessage): bool;
}
