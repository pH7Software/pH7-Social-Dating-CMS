<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

class SmsProvider
{
    protected string $sSenderNumber;

    protected string $sApiToken;

    protected ?string $sApiId;

    /**
     * @param string $sSenderNumber
     * @param string $sApiToken
     * @param string|null $sApiId At the moment, this parameter is only used by Twilio API.
     */
    public function __construct(string $sSenderNumber, string $sApiToken, ?string $sApiId = null)
    {
        $this->sSenderNumber = $sSenderNumber;
        $this->sApiToken = $sApiToken;
        $this->sApiId = $sApiId;
    }
}
