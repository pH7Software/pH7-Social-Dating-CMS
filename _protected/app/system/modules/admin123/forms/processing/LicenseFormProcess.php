<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\License, PH7\Framework\Mvc\Request\Http;

class LicenseFormProcess extends Form
{

    public function __construct($iLicenseId)
    {
        parent::__construct();

        $oLicense = new License;
        $sKey = $this->httpRequest->post('basic_key', Http::NO_CLEAN) . ';' . $this->httpRequest->post('copyright_key', Http::NO_CLEAN) . ';';

        if (!$this->str->equals($sKey, $oLicense->get($iLicenseId)))
        {
            $oLicense->save($sKey, $iLicenseId);

            // Clean caches to remove the copyright notices
            $this->file->deleteDir(PH7_PATH_CACHE . Framework\Cache\Cache::CACHE_DIR);
            $this->file->deleteDir(PH7_PATH_CACHE . Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl::COMPILE_DIR);
            $this->file->deleteDir(PH7_PATH_CACHE . Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl::CACHE_DIR);
        }

        \PFBC\Form::setSuccess('form_license', t('Your License Key was saved successfully!'));
    }

}
