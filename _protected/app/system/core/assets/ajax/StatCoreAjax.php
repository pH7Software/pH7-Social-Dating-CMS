<?php
/**
 * @title          Stat Ajax Class
 * @desc           Class of statistical data for the CMS in Ajax.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        0.6
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\JustHttp\StatusCode;

class StatCoreAjax
{
    private UserCoreModel $oUserModel;

    private string|int $mOutput;

    public function __construct()
    {
        $this->oUserModel = new UserCoreModel;
        $this->init();
    }

    public function display(): string|int
    {
        return $this->mOutput;
    }

    private function init(): void
    {
        $sType = (new HttpRequest)->post('type');

        switch ($sType) {
            case 'total_users':
                $this->mOutput = $this->oUserModel->total();
                break;

            // If we receive another invalid value, we display a message with a HTTP header.
            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
    }
}

echo (new StatCoreAjax)->display();
