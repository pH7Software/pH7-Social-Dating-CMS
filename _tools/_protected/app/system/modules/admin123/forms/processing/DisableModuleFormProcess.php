<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Module as ModuleModel;

class DisableModuleFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oModuleModel = new ModuleModel;

        // First, disable all mods as uncheckboxes elements aren't send through the form
        $this->disableMods($oModuleModel);

        // Then, enable the mods selected to be enabled
        foreach ($this->httpRequest->post('module_id') as $iModId) {
            $oModuleModel->update($iModId, ModuleModel::YES); // Need to be string because in DB it's an "enum" type
        }
        unset($oModuleModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_module', t('Module Status saved!'));
    }

    private function disableMods(ModuleModel $oModuleModel)
    {
        foreach ($oModuleModel->get() as $oMod) {
            // Need to be string because in DB it's an "enum" type
            $oModuleModel->update($oMod->moduleId, ModuleModel::NO);
        }
    }

    private function clearCache()
    {
        (new Cache)->start(ModuleModel::CACHE_GROUP, null, null)->clear();
    }
}
