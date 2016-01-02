<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */
namespace PH7;

use PH7\Framework\Mvc\Model\Module as ModuleModel;

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
        foreach ($oModuleData as $oId)
        {
            if ((int)$oId->enabled === 1) {
                $aSelectedMods[] = $oId->moduleId;
            }

            $aModuleNames[$oId->moduleId] = ucfirst($oId->folderName);
        }

        $oForm = new \PFBC\Form('form_module', 700);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_module', 'form_module'));
        $oForm->addElement(new \PFBC\Element\Token('module'));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Enable/Disable Modules'), 'module_id', $aModuleNames, array('value' => $aSelectedMods)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
