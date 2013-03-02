<?php
/**
 * @title          Config File Core Processing Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 * @version        1.1
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Url\HeaderUrl;

class ConfigFileCoreFormProcessing extends Form
{

    private $sMsg;

    /**
     * @param string $sConfigVar Specify the variable in the INI file where module options. Default module.setting
     * @param string $sIniFile The path of INI config file.
     * @return void
     */
    public function __construct($sConfigVar, $sIniFile)
    {
        parent::__construct();

        $aOldData = parse_ini_file($sIniFile, true);
        $sData = file_get_contents($sIniFile);

        foreach($this->httpRequest->post('config') as $sKey => $sVal)
          $sData = str_replace($sKey . ' = ' . $aOldData[$sConfigVar][$sKey], $sKey . ' = ' . $sVal,  $sData);

        // Check and correct the file permission if necessary.
        $this->file->chmod($sIniFile, 0644);

        if($this->file->save($sIniFile, $sData))
            HeaderUrl::redirect($this->httpRequest->previousPage(), ('The file content was saved successfully!'));
        else
            HeaderUrl::redirect($this->httpRequest->previousPage(), t('The file content could not be saved!'), 'error');

        // Check and correct the file permission if necessary.
        $this->file->chmod($sIniFile, 0644);

    }

}
