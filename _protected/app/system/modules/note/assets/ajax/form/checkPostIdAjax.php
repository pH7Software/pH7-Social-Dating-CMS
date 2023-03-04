<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Note / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Datatype\Type;
use PH7\Framework\Mvc\Request\Http;

$oHttpRequest = new Http;
$iStatus = 0; // Error Default Value

if ($oHttpRequest->postExists('post_id')) {
    $sPostId = $oHttpRequest->post('post_id');
    $iProfileId = $oHttpRequest->post('profile_id', Type::INTEGER);
    $iStatus = (new Note)->checkPostId($sPostId, $iProfileId, new NoteModel) ? 1 : 0;
}

echo json_encode(['status' => $iStatus]);
