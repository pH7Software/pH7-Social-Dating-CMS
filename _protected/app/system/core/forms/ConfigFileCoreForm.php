<?php
/**
 * @title          Generate a dynamic form from INI files
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Various as FileHelper;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

class ConfigFileCoreForm
{
    const CONFIG_FILE = 'config.ini';
    const CONFIG_SETTING_SECTION = 'module.setting';

    const CONFIG_KEYS = [
        'general_cache' => 'enable.general.cache',
        'html_tpl_cache' => 'enable.html.tpl.cache',
        'static_cache' => 'enable.static.cache',
        'static_data_uri' => 'enable.static.data_uri'
    ];

    /**
     * @param string $sConfigVar Specify the variable in the INI file where module options. Default: module.setting
     * @param string|null $sConfigPath Specify the path of INI file configuration WITHOUT "config.ini". The default value is the current configuration module file.
     *
     * @return void
     */
    public static function display($sConfigVar = self::CONFIG_SETTING_SECTION, $sConfigPath = null)
    {
        $sIniFile = empty($sConfigPath) ? Registry::getInstance()->path_module_config . static::CONFIG_FILE : $sConfigPath . static::CONFIG_FILE;

        if (isset($_POST['submit_config'])) {
            if (\PFBC\Form::isValid($_POST['submit_config'])) {
                new ConfigFileCoreFormProcess($sConfigVar, $sIniFile);
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_config');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_config', 'form_config'));
        $oForm->addElement(new \PFBC\Element\Token('config'));

        $aData = parse_ini_file($sIniFile, true);
        foreach ($aData[$sConfigVar] as $sKey => $sVal) {
            $sLabel = self::getLabelText($sKey);

            if (false !== strpos($sKey, 'enable')) {
                $oForm->addElement(new \PFBC\Element\Select($sLabel, 'config[' . $sKey . ']', [1 => t('Enable'), 0 => t('Disable')], ['value' => $sVal]));
            } elseif (false !== strpos($sKey, 'email')) {
                $oForm->addElement(new \PFBC\Element\Email($sLabel, 'config[' . $sKey . ']', ['value' => $sVal]));
            } elseif (false !== strpos($sKey, 'environment')) {
                $oForm->addElement(new \PFBC\Element\Select($sLabel, 'config[' . $sKey . ']', ['production' => t('Production'), 'development' => t('Development')], ['description' => t('If you see "Internal Server Error" message on your site, please set to "development" mode in order to see the details of the error. If your site is on production (and visible by everyone) please set it to the production mode for security reason.'), 'value' => $sVal]));
            } elseif (ctype_digit($sVal)) {
                $oForm->addElement(new \PFBC\Element\Number($sLabel, 'config[' . $sKey . ']', ['step' => 'any', 'value' => $sVal]));
            } else {
                $oForm->addElement(new \PFBC\Element\Textbox($sLabel, 'config[' . $sKey . ']', ['value' => $sVal]));
            }
        }
        unset($aData);

        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

    /**
     * @param string $sKey
     *
     * @return string
     */
    private static function getLabelText($sKey)
    {
        if (self::isLabelTextCustomed($sKey)) {
            return self::getCustomLabelText($sKey);
        }

        $sLabel = self::cleanLabelText($sKey);

        return (new Str)->upperFirstWords($sLabel);
    }

    /**
     * @param string $sKey
     *
     * @return string
     */
    private static function getCustomLabelText($sKey)
    {
        if ($sKey === self::CONFIG_KEYS['general_cache']) {
            return t('Enable General Cache. Database caching and other expensive server calculations');
        }

        if ($sKey === self::CONFIG_KEYS['html_tpl_cache']) {
            return t('Enable HTML Cache. Caches some HTML pages (e.g., TOS, privacy, site map, ... pages)');
        }

        if ($sKey === self::CONFIG_KEYS['static_cache']) {
            return t('Enable Static Cache. Used to store compressed/minified JS/CSS files');
        }

        if ($sKey === self::CONFIG_KEYS['static_data_uri']) {
            return t('Enable data-URIs; Converts images to base64 (if file size is lower than %0%)', FileHelper::bytesToSize(Gzip::MAX_IMG_SIZE_BASE64_CONVERTOR));
        }

        return self::cleanLabelText($sKey);
    }

    /**
     * @param string $sKey
     *
     * @return string
     */
    private static function cleanLabelText($sKey)
    {
        return str_replace(['.', '_'], ' ', $sKey);
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    private static function isLabelTextCustomed($sKey)
    {
        return in_array($sKey, self::CONFIG_KEYS, true);
    }
}
