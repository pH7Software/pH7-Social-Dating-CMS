<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\License as LicenseModel;

class LicenseFormProcess extends Form
{
    public function __construct($iLicenseId)
    {
        parent::__construct();

        $oLicenseModel = new LicenseModel;
        $sKey = $this->httpRequest->post('copyright_key');

        if (!$this->str->equals($sKey, $oLicenseModel->get($iLicenseId))) {
            $oLicenseModel->save($sKey, $iLicenseId);

            // Clean caches to remove the copyright notices
            $this->file->deleteDir(PH7_PATH_CACHE . PH7Tpl::COMPILE_DIR);
            $this->file->deleteDir(PH7_PATH_CACHE . PH7Tpl::CACHE_DIR);
        }
        unset($oLicenseModel);
    }
}
