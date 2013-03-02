<?php
/**
 * @title          Config File Core Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 * @version        1.1
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Str\Str, PH7\Framework\Registry\Registry;

class ConfigFileCoreForm
{

    /**
     * @param string $sConfigVar Specify the variable in the INI file where module options. Default module.setting
     * @param string $sConfigPath Specify the path of INI file configuration WITHOUT "config.ini". The default value is the current configuration file module. Default NULL
     * @return void
     */
    public static function display($sConfigVar = 'module.setting', $sConfigPath = null)
    {
        $sConfigFile = 'config.ini';

        $sIniFile = (empty($sConfigPath)) ? Registry::getInstance()->path_module_config . PH7_DS . $sConfigFile : $sConfigPath . $sConfigFile;
        $aData = parse_ini_file($sIniFile, true);
        $rData = file_get_contents($sIniFile);

        if (isset($_POST['submit_config']))
        {
            if (\PFBC\Form::isValid($_POST['submit_config']))
                new ConfigFileCoreFormProcessing($sConfigVar, $sIniFile);

            Framework\Url\HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_config', 600);
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_config', 'form_config'));
        $oForm->addElement(new \PFBC\Element\Token('config'));

        foreach ($aData[$sConfigVar] as $sKey => $sVal)
        {
            $sLabel = str_replace(array('.', '_'), ' ', $sKey);
            $sLabel = (new Str)->upperFirstWords($sLabel);

            if (false !== strpos($sKey, 'enable'))
                $oForm->addElement(new \PFBC\Element\Select($sLabel, 'config[' . $sKey . ']', array(1 => t('Enable'), 0 => t('Disable')), array('value' => $sVal)));
            elseif (false !== strpos($sKey, 'email'))
                $oForm->addElement(new \PFBC\Element\Email($sLabel, 'config[' . $sKey . ']', array('value' => $sVal)));
            elseif (is_numeric($sVal))
                $oForm->addElement(new \PFBC\Element\Number($sLabel, 'config[' . $sKey . ']', array('value' => $sVal)));
            else
                $oForm->addElement(new \PFBC\Element\Textbox($sLabel, 'config[' . $sKey . ']', array('value' => $sVal)));
        }


        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
