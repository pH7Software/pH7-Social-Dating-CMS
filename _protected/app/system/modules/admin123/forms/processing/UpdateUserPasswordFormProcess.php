<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class UpdateUserPasswordFormProcess extends Form
{
    /**
     * @param string $sUserEmail
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     *
     * @internal Need to use Http::NO_CLEAN arg in Http::post() since password might contains special character like "<" and will otherwise be converted to HTML entities
     */
    public function __construct($sUserEmail)
    {
        parent::__construct();

        if ($this->httpRequest->post('new_password', Http::NO_CLEAN) !== $this->httpRequest->post('new_password2', Http::NO_CLEAN)) {
            \PFBC\Form::setError('form_update_password', t("The passwords don't match."));
        } else {
            if ($this->updatePassword($sUserEmail)) {
                Header::redirect(
                    Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                    t('The user password has been successfully changed!')
                );
            } else {
                \PFBC\Form::setError(
                    'form_update_password',
                    t("The password couldn't be updated for the account with email: %0%", $sUserEmail)
                );
            }
        }
    }

    /**
     * @param string $sUserEmail
     *
     * @return bool
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function updatePassword($sUserEmail)
    {
        return (new UserCoreModel)->changePassword(
            $sUserEmail,
            $this->httpRequest->post('new_password', Http::NO_CLEAN),
            DbTableName::MEMBER
        );
    }
}
