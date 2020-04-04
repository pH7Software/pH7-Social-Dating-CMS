<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class ProtectedFileFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $bStatus = $this->file->save(
            PH7_PATH_PROTECTED . $this->httpRequest->get('file'),
            $this->httpRequest->post('content', Http::NO_CLEAN)
        );

        $sMsg = $bStatus ? t('Changes saved!') : t('The file could not be saved. Please check your file permissions (must be in  write mode)');
        $sMsgType = $bStatus ? Design::SUCCESS_TYPE : Design::ERROR_TYPE;

        Header::redirect(
            $this->httpRequest->previousPage(),
            $sMsg,
            $sMsgType
        );
    }
}
