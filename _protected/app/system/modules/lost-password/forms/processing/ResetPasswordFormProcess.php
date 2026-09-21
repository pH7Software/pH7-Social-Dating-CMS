<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class ResetPasswordFormProcess extends Form
{
    public function __construct(string $sMod, string $sEmail, string $sToken)
    {
        parent::__construct();

        $sPassword = $this->httpRequest->post('new_password', Http::NO_CLEAN);
        if (!is_string($sPassword) || $sPassword === '' || $sPassword !== $this->httpRequest->post('new_password2', Http::NO_CLEAN)) {
            \PFBC\Form::setError('form_reset_password', t("The passwords don't match."));

            return;
        }

        $sTable = Various::convertModToTable($sMod);
        $iProfileId = (new PasswordResetModel())->reset($sEmail, $sToken, $sPassword, $sTable);
        if ($iProfileId === null) {
            \PFBC\Form::setError('form_reset_password', t('This password reset link is invalid or expired. Please request a new one.'));

            return;
        }

        (new UserCore())->clearReadProfileCache($iProfileId, $sTable);
        $this->session->regenerateId();
        $sLoginUrl = $sMod === 'affiliate'
            ? Uri::get('affiliate', 'home', 'login')
            : Uri::get($sMod, 'main', $sMod === 'user' ? 'index' : 'login');
        Header::redirect($sLoginUrl, t('Your password has been changed. Please sign in with your new password.'));
    }
}
