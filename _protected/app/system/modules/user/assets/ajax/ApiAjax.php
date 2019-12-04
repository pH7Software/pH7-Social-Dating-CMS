<?php
/**
 * @title          User API Ajax Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use Teapot\StatusCode;

class ApiAjax
{
    /** @var UserCore */
    private $oUser;

    /** @var mixed */
    private $mOutput;

    public function __construct()
    {
        $this->oUser = new UserCore;
        $this->init();
    }

    public function display()
    {
        return $this->mOutput;
    }

    private function init()
    {
        $oHttpRequest = new HttpRequest;
        $sParam = $oHttpRequest->post('param');
        $sType = $oHttpRequest->post('type');
        unset($oHttpRequest);

        switch ($sType) {
            case 'profile_link':
                $this->mOutput = $this->oUser->getProfileLink($sParam);
                break;

            // If we receive another invalid value, we display a message with a HTTP header.
            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
    }
}

echo (new ApiAjax)->display();
