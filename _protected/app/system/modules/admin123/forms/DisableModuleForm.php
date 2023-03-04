<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\Hidden;
use PFBC\Element\Token;
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

        foreach ($oModuleData as $oModDetails) {
            // Ignore the default core module (since it cannot be disabled)
            if ($oModDetails->folderName === $sDefaultCoreMod) {
                continue;
            }

            if ($oModDetails->enabled === ModuleModel::YES) {
                $aSelectedMods[] = $oModDetails->moduleId;
            }

            $sAdditionalText = '';
            if ($oModDetails->premiumMod === ModuleModel::YES) {
                $sAdditionalText .= ' • <a class="small" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'general') . '#p=api">' . t('Change the default Chat by yours') . '</a>';
            }

            $aModuleNames[$oModDetails->moduleId] = $oModDetails->moduleTitle . $sAdditionalText;
        }
        unset($oModuleData);

        $oForm = new \PFBC\Form('form_module');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_module', 'form_module'));
        $oForm->addElement(new Token('module'));
        $oForm->addElement(new Checkbox('', 'module_id', $aModuleNames, ['value' => $aSelectedMods]));
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
