<?php
/**
 * @title          Add Users; Process Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AddUserFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sBirthDate = $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d');

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'password' => $this->httpRequest->post('password', Http::NO_CLEAN),
            'first_name' => $this->httpRequest->post('first_name'),
            'last_name' => $this->httpRequest->post('last_name'),
            'sex' => $this->httpRequest->post('sex'),
            'match_sex' => $this->httpRequest->post('match_sex'),
            'birth_date' => $sBirthDate,
            'country' => $this->httpRequest->post('country'),
            'city' => $this->httpRequest->post('city'),
            'state' => $this->httpRequest->post('state'),
            'zip_code' => $this->httpRequest->post('zip_code'),
            'punchline' => $this->httpRequest->post('punchline'),
            'description' => $this->httpRequest->post('description', Http::ONLY_XSS_CLEAN),
            'ip' => Ip::get()
        ];
        $iProfileId = (new UserCoreModel)->add($aData);

        if (!empty($_FILES['avatar']['tmp_name'])) {
            (new UserCore)->setAvatar($iProfileId, $aData['username'], $_FILES['avatar']['tmp_name'], 1);
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            t('User successfully added.')
        );
    }
}
