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
    const YES_VALUE = '1';

    const DEV_STAGE_MODS = [
        'connect'
    ];

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

            if ($oData->enabled === self::YES_VALUE) {
                $aSelectedMods[] = $oData->moduleId;
            }

            $sAdditionalText = '';
            if ($oData->premiumMod === self::YES_VALUE) {
                $sAdditionalText .= ' • <a class="small" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'general') . '#p=api">' . t('Change the default Chat by yours') . '</a>';
            }

            if (in_array($oData->folderName, self::DEV_STAGE_MODS, true)) {
                $sAdditionalText .= '<span class="small red"> • ' . t('Only for development purpose to test it before <a href="%0%">opening a PR</a>. <a href="%1%">Social APIs</a> have to be updated.', 'https://github.com/pH7Software/pH7-Social-Dating-CMS/pulls', 'https://github.com/pH7Software/pH7-Social-Dating-CMS/blob/master/_protected/app/system/modules/connect/inc/class/') . '</span>';
            }

            $aModuleNames[$oData->moduleId] = $oData->moduleTitle . $sAdditionalText;
        }
        unset($oModuleData);

        $oForm = new \PFBC\Form('form_module');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_module', 'form_module'));
        $oForm->addElement(new \PFBC\Element\Token('module'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'module_id', $aModuleNames, ['value' => $aSelectedMods]));
        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }
}
