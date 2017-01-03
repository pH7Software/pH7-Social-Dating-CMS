<?php
/**
 * @title            Optimization Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout
 * @version          1.0
 */

namespace PH7\Framework\Layout;
defined('PH7') or exit('Restricted access');

class Optimization
{

    /**
     * Data URI scheme - base64 encoding.
     *
     * @param string $sFile
     * @return string Returns format: data:[<MIME-type>][;base64],<data>
     */
    public static function dataUri($sFile)
    {
        $oFile = new \PH7\Framework\File\File();
        // Switch to right MIME-type
        $sExt = $oFile->getFileExt($sFile);
        $sMimeType = $oFile->getMimeType($sExt);
        unset($oFile);

        $sBase64 = base64_encode(file_get_contents($sFile));
        return "data:$sMimeType;base64,$sBase64";
    }


    /**
     * Scan the path ($sDir) of all file-references found.
     * Note: This function is a slightly modified version from Christian Schepp Schaefer's function (CSS JS booster).
     *
     * @param string $sFile Contents to scan.
     * @param string $sDir Folder name to prepend.
     * @return string Content with adjusted paths.
     */
    public static function cssDataUriCleanup($sFile, $sDir)
    {
        // Scan for any left file-references and adjust their path
        $sRegexUrl = '/(url\([\'"]??)([^\'"\)]+?\.[^\'"\)]+?)([\'"]??\))/msi';

        preg_match_all($sRegexUrl, $sFile, $aHit, PREG_PATTERN_ORDER);

        for ($i=0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++)
        {
            $sSearch = $aHit[1][$i] . $aHit[2][$i] . $aHit[3][$i];

            $sReplace = $sDir . $aHit[1][$i];
            $sReplace .= $aHit[2][$i] . $aRreffer[3][$i];

            if (
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),0,5) != 'http:' &&
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),0,6) != 'https:' &&
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),0,5) != 'data:' &&
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),0,6) != 'mhtml:' &&
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),0,1) != '/' &&
                substr(str_replace(array('"', "'"), '', $aHit[2][$i]),strlen(str_replace(array('"',"'"),'', $aHit[2][$i])) - 4,4) != '.htc'
            ) $sFile = str_replace($sSearch, $sReplace, $sFile);
        }
        return $sFile;
    }

}
