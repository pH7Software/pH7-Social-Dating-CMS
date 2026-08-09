<?php
/**
 * @title          Config File Core Process Form
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 * @version        1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class ConfigFileCoreFormProcess extends Form
{
    /**
     * @param string $sConfigVar Specify the variable in the INI file where module options. Default module.setting
     * @param string $sIniFile The path of INI config file.
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($sConfigVar, $sIniFile)
    {
        parent::__construct();

        $aOldData = parse_ini_file($sIniFile, true);
        $sData = file_get_contents($sIniFile);
        $mPostedConfig = $this->httpRequest->post('config', Http::NO_CLEAN);

        if (
            !is_array($aOldData) ||
            !isset($aOldData[$sConfigVar]) ||
            !is_array($aOldData[$sConfigVar]) ||
            !is_string($sData) ||
            !is_array($mPostedConfig)
        ) {
            Header::redirect(
                $this->httpRequest->previousPage(),
                t('The configuration could not be read. Check the submitted form and config file.'),
                Design::ERROR_TYPE
            );
            return;
        }

        foreach ($mPostedConfig as $sKey => $mValue) {
            if (!is_string($sKey) || !array_key_exists($sKey, $aOldData[$sConfigVar]) || !is_scalar($mValue)) {
                continue;
            }

            $sValue = (string)$mValue;
            if (self::shouldPreserveExistingValue($sKey, $sValue)) {
                continue;
            }

            if (preg_match('/[\r\n]/', $sValue)) {
                Header::redirect(
                    $this->httpRequest->previousPage(),
                    t('Configuration values cannot contain line breaks.'),
                    Design::ERROR_TYPE
                );
            }

            $sData = self::replaceConfigValue($sData, $sKey, $sValue);
        }

        // Check and correct the file permission if necessary.
        $this->file->chmod($sIniFile, Chmod::MODE_WRITE_READ);

        $sRedirectUrl = $this->httpRequest->previousPage();
        if ($this->file->save($sIniFile, $sData)) {
            Header::redirect($sRedirectUrl, t('Configuration updated!'));
        } else {
            Header::redirect(
                $sRedirectUrl,
                t('The config file could not be saved. Please check your file permissions (must be in write mode)'),
                Design::ERROR_TYPE
            );
        }

        // Check again and correct the file permission if necessary.
        $this->file->chmod($sIniFile, Chmod::MODE_WRITE_READ);
    }

    private static function replaceConfigValue(string $sData, string $sKey, string $sValue): string
    {
        $sEscapedValue = str_replace(['\\', '$', '"'], ['\\\\', '\\$', '\\"'], $sValue);
        $sSerializedValue = is_numeric($sValue) && !ConfigFileCoreForm::isSensitiveKey($sKey)
            ? $sValue
            : '"' . $sEscapedValue . '"';
        $sPattern = '/^([ \t]*' . preg_quote($sKey, '/') . '[ \t]*=[ \t]*)[^\r\n]*$/m';

        $sUpdatedData = preg_replace_callback(
            $sPattern,
            static fn (array $aMatches): string => $aMatches[1] . $sSerializedValue,
            $sData,
            1
        );

        return is_string($sUpdatedData) ? $sUpdatedData : $sData;
    }

    private static function shouldPreserveExistingValue(string $sKey, string $sValue): bool
    {
        return ConfigFileCoreForm::isSensitiveKey($sKey) && $sValue === '';
    }
}
