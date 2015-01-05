<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http, PH7\Framework\Url\Header;

class ProtectedFileFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $bStatus = $this->file->save(PH7_PATH_PROTECTED . $this->httpRequest->get('file'), $this->httpRequest->post('content', Http::NO_CLEAN));
        $sMsg = ($bStatus) ? t('The file content was saved successfully!') : t('The file content could not be saved!');
        $sMsgType = ($bStatus) ? 'success' : 'error';

        Header::redirect($this->httpRequest->previousPage(), $sMsg, $sMsgType);
    }

}
