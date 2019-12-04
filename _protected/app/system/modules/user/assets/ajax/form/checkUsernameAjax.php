<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\Validate\Validate;

$oHttpRequest = new Http;

$iStatus = 0; // Error Default Value

if ($oHttpRequest->postExists('username')) {
    $iStatus = ((new Validate)->username($oHttpRequest->post('username'), DbConfig::getSetting('minUsernameLength'), DbConfig::getSetting('maxUsernameLength'))) ? 1 : 0;
}

echo json_encode(['status' => $iStatus]);
