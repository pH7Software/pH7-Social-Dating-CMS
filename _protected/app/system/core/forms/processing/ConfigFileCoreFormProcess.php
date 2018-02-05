<?php
/**
 * @title          Config File Core Process Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 * @version        1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Url\Header;

class ConfigFileCoreFormProcess extends Form
{
    /**
     * @param string $sConfigVar Specify the variable in the INI file where module options. Default module.setting
     * @param string $sIniFile The path of INI config file.
     */
    public function __construct($sConfigVar, $sIniFile)
    {
        parent::__construct();

        $aOldData = parse_ini_file($sIniFile, true);
        $sData = file_get_contents($sIniFile);

        foreach ($this->httpRequest->post('config') as $sKey => $sVal) {
            $sData = str_replace($sKey . ' = ' . $aOldData[$sConfigVar][$sKey], $sKey . ' = ' . $sVal, $sData);

            /**
             * ----- Replacement with quotes -----
             * For non-alphanumeric characters and especially for special  characters.
             * For example, it is very important to put quotes between the dollar sign "$", otherwise you'll get errors in the parsing of INI files.
             */
            $sData = str_replace($sKey . ' = "' . $aOldData[$sConfigVar][$sKey] . '"', $sKey . ' = "' . $sVal . '"', $sData);
        }

        // Check and correct the file permission if necessary.
        $this->file->chmod($sIniFile, File::READ_WRITE_CHMOD_OCTAL_DIGIT);

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
        $this->file->chmod($sIniFile, File::READ_WRITE_CHMOD_OCTAL_DIGIT);
    }
}
