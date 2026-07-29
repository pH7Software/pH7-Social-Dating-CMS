<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Controller\Controller as FwkController;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;

abstract class Controller extends FwkController
{
    /**
     * Reject a state-changing LinkCoreForm/ConfirmCoreForm request unless its
     * route-specific CSRF token is valid.
     */
    protected function requireActionToken(string $sModule, string $sController, string $sAction): void
    {
        $this->requireUrlToken(Uri::get($sModule, $sController, $sAction));
    }

    /**
     * Validate forms that intentionally submit back to the complete current URL.
     */
    protected function requireCurrentUrlToken(): void
    {
        $this->requireUrlToken($this->httpRequest->currentUrl());
    }

    private function requireUrlToken(string $sUrl): void
    {
        if ((new Token())->check(Token::getNameFromUrl($sUrl))) {
            return;
        }

        Header::redirect(
            PH7_URL_ROOT,
            Form::errorTokenMsg(),
            Design::ERROR_TYPE
        );
    }
}
