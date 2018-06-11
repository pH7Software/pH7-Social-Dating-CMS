<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2016-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Module as ModuleModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class DisableModuleForm
{
    public static function display()
    {
        if (isset($_POST['submit_module'])) {
            if (\PFBC\Form::isValid($_POST['submit_module'])) {
                new DisableModuleFormProcess;
            }

            Header::redirect();
        }

        $oModuleData = (new ModuleModel)->get();
        $aModuleNames = [];
        $aSelectedMods = [];
        $sDefaultCoreMod = DbConfig::getSetting('defaultSysModule');

        foreach ($oModuleData as $oData) {
            // Ignore the default core module (since it cannot be disabled)
            if ($oData->folderName === $sDefaultCoreMod) {
                continue;
            }

            if ((int)$oData->enabled === 1) {
                $aSelectedMods[] = $oData->moduleId;
            }

            $sAdditionalText = '';
            if ((int)$oData->premiumMod === 1) {
                $sAdditionalText .= ' • <a class="small" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'general') . '#p=api">' . t('Change the default Chat by yours') . '</a>';
            }

            if ($oData->folderName === 'connect') {
                $sAdditionalText .= '<span class="small"> • <a class="underline" href="http://ph7cms.com/better-not-enable-connect-mod/">' . t('not recommended to enable it') . '</a></span>';
            }

            $aModuleNames[$oData->moduleId] = $oData->moduleTitle . $sAdditionalText;
        }
        unset($oModuleData);

        $oForm = new \PFBC\Form('form_module');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_module', 'form_module'));
        $oForm->addElement(new \PFBC\Element\Token('module'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'module_id', $aModuleNames, ['value' => $aSelectedMods]));
        $oForm->addElement(new \PFBC\Element\Button(t('Save')));
        $oForm->render();
    }
}
