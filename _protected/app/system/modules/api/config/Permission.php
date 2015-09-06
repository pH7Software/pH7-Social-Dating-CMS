<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Api / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if (!Framework\Api\Tool::checkAccess($this->config, $this->httpRequest))
        {
            Framework\Http\Http::setHeadersByCode(403);
            exit('Your API key and/or the URL of your external application don\'t match with the one in the pH7CMS\'s configuration system!');
        }
    }

}
