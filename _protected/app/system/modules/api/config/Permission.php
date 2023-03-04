<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7builder.com
 * @link           http://github.com/pH7Software/pH7Builder-HTTP-REST-Push-Data
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Api\Tool;
use PH7\Framework\Http\Http;
use PH7\JustHttp\StatusCode;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!Tool::checkAccess($this->config, $this->httpRequest)) {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);
            t("Your API key and/or the URL of your external application don't match with the one in your pH7Builder's configuration system!");
            exit;
        }
    }
}
