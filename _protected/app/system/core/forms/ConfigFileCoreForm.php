<?php

/**
 * @title          Generate a dynamic form from INI files
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Button;
use PFBC\Element\Currency;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\Number;
use PFBC\Element\Password;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PH7\Framework\File\Various as FileHelper;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

class ConfigFileCoreForm
{
    public const CONFIG_FILE = 'config.ini';
    public const CONFIG_SETTING_SECTION = 'module.setting';

    public const CONFIG_KEYS = [
        'general_cache' => 'enable.general.cache',
        'html_tpl_cache' => 'enable.html.tpl.cache',
        'static_cache' => 'enable.static.cache',
        'static_data_uri' => 'enable.static.data_uri',
        'currency_sign' => 'currency_sign'
    ];

    private const SENSITIVE_KEY_PARTS = [
        'api_token',
        'password',
        'private_key',
        'secret'
    ];

    /**
     * @param string      $sConfigVar  Specify the variable in the INI file where module options. Default: module.setting
     * @param string|null $sConfigPath Specify the path of INI file configuration WITHOUT "config.ini". The default value is the current configuration module file.
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
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
        $oForm->addElement(new Hidden('submit_config', 'form_config'));
        $oForm->addElement(new Token('config'));

        $aData = parse_ini_file($sIniFile, true);
        foreach ($aData[$sConfigVar] as $sKey => $sVal) {
            $sLabel = self::getLabelText($sKey);
            $sFieldName = 'config[' . $sKey . ']';
            $aProperties = self::getFieldProperties($sKey, $sVal);

            if (self::isSensitiveKey($sKey)) {
                $oForm->addElement(new Password($sLabel, $sFieldName, $aProperties));
            } elseif (false !== strpos($sKey, 'enable')) {
                $oForm->addElement(new Select($sLabel, $sFieldName, [1 => t('Enable'), 0 => t('Disable')], $aProperties));
            } elseif (false !== strpos($sKey, 'email')) {
                $oForm->addElement(new Email($sLabel, $sFieldName, $aProperties));
            } elseif (false !== strpos($sKey, 'environment')) {
                $aProperties['description'] = t('If you see "Internal Server Error" message on your site, please set to "development" mode in order to see the details of the error. If your site is on production (and visible by everyone) please set it to the production mode for security reasons.');
                $oForm->addElement(new Select($sLabel, $sFieldName, ['production' => t('Production'), 'development' => t('Development')], $aProperties));
            } elseif (false !== strpos($sKey, 'currency_code')) {
                $oForm->addElement(new Currency($sLabel, $sFieldName, $aProperties));
            } elseif (is_numeric($sVal)) {
                $aProperties['step'] = 'any';
                $oForm->addElement(new Number($sLabel, $sFieldName, $aProperties));
            } else {
                $oForm->addElement(new Textbox($sLabel, $sFieldName, $aProperties));
            }
        }
        unset($aData);

        $oForm->addElement(new Button());
        $oForm->render();
    }

    public static function isSensitiveKey(string $sKey): bool
    {
        $sKey = strtolower($sKey);
        if ($sKey === 'youtube.key') {
            return true;
        }

        foreach (self::SENSITIVE_KEY_PARTS as $sSensitiveKeyPart) {
            if (str_contains($sKey, $sSensitiveKeyPart)) {
                return true;
            }
        }

        return false;
    }

    private static function getFieldProperties(string $sKey, mixed $mValue): array
    {
        if (self::isSensitiveKey($sKey)) {
            return [
                'autocomplete' => 'new-password',
                'description' => t('For security, the saved value is not displayed. Leave blank to keep it unchanged.'),
                'placeholder' => empty($mValue) ? t('Not configured') : t('Configured'),
                'spellcheck' => 'false',
                'value' => ''
            ];
        }

        $aProperties = ['value' => $mValue];
        if ($sKey === 'sandbox.enabled') {
            $aProperties['description'] = t('Keep sandbox mode enabled until every configured gateway has passed a test payment.');
        } elseif ($sKey === 'vat_rate') {
            $aProperties['description'] = t('Tax is specific to your business and jurisdiction. Leave this at 0 until the correct percentage is confirmed; the configured rate is added at checkout.');
        } elseif ($sKey === 'stripe.enabled') {
            $aProperties['description'] = t('Stripe checkout is disabled in pH7Builder 18.6.0 because the bundled legacy flow is not SCA-ready. Keep it disabled until the integration is migrated to Stripe Checkout Sessions or Payment Intents.');
        } elseif ($sKey === '2co.enabled') {
            $aProperties['description'] = t('2Checkout is disabled in pH7Builder 18.6.0 because the bundled legacy flow requires migration to the 2Checkout API 6.0.');
        } elseif (str_ends_with($sKey, '.enabled') && $sKey !== 'log_file.enabled') {
            $aProperties['description'] = t('Enable this gateway only after its credentials are configured and a sandbox payment succeeds.');
        }

        return $aProperties;
    }

    /**
     * @param string $sKey
     *
     * @return string
     */
    private static function getLabelText($sKey)
    {
        if (self::isCustomLabelText($sKey)) {
            return self::getCustomLabelText($sKey);
        }

        $sLabel = self::cleanLabelText($sKey);

        return (new Str())->upperFirstWords($sLabel);
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

        if ($sKey === self::CONFIG_KEYS['currency_sign']) {
            return t('Currency Sign (for display purposes only)');
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
    private static function isCustomLabelText($sKey)
    {
        return in_array($sKey, self::CONFIG_KEYS, true);
    }
}
