<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Controller
 * @link           http://ph7cms.com
 * @link           http://github.com/pH7Software/pH7CMS-HTTP-REST-Push-Data
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Api\Tool;
use PH7\Framework\Http\Http;
use Teapot\StatusCode;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!Tool::checkAccess($this->config, $this->httpRequest)) {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);
            t("Your API key and/or the URL of your external application don't match with the one in your pH7CMS's configuration system!");
            exit;
        }
    }
}
