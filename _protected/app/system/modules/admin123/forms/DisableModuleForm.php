<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Model\Module as ModuleModel;

class DisableModuleForm
{

    public static function display()
    {
        if (isset($_POST['submit_module']))
        {
            if (\PFBC\Form::isValid($_POST['submit_module']))
                new DisableModuleFormProcess;

            Framework\Url\Header::redirect();
        }

        $oModuleData = (new ModuleModel)->get();
        $aModuleNames = [];
        $aSelectedMods = [];
        $sDefaultCoreMod = DbConfig::getSetting('defaultSysModule');

        foreach ($oModuleData as $oData)
        {
            // Ignore the default core module (since it cannot be disabled)
            if ($oData->folderName === $sDefaultCoreMod)
                continue;

            if ((int)$oData->enabled === 1) {
                $aSelectedMods[] = $oData->moduleId;
            }

            $sPremiumText = '';
            if ((int)$oData->premiumMod === 1)
            {
                $sPremiumText = ' – (<a class="italic darkred" href="' . Core::SOFTWARE_LICENSE_KEY_URL . '">' . t('Premium Module') . '</a>)';
                $sPremiumText .= ' • <a class="small" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'general') . '#p=api">' . t('Change the default API service by yours') . '</a>';
            }

            $aModuleNames[$oData->moduleId] = $oData->moduleTitle . $sPremiumText;
        }
        unset($oModuleData);

        $oForm = new \PFBC\Form('form_module');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_module', 'form_module'));
        $oForm->addElement(new \PFBC\Element\Token('module'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'module_id', $aModuleNames, array('value' => $aSelectedMods)));
        $oForm->addElement(new \PFBC\Element\Button(t('Save')));
        $oForm->render();
    }

}