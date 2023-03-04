<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout
 */

namespace PH7\Framework\Layout;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;

class Optimization
{
    const REGEX_CSS_IMPORT_URL_PATTERN = '/(url\([\'"]??)([^\'"\)]+?\.[^\'"\)]+?)([\'"]??\))/msi';

    /**
     * Data URI scheme - base64 encoding.
     *
     * @param string $sFile
     * @param File $oFile
     *
     * @return string Returns format: data:[<MIME-type>][;base64],<data>
     */
    public static function dataUri($sFile, File $oFile)
    {
        // Switch to right MIME-type
        $sExt = $oFile->getFileExt($sFile);
        $sMimeType = $oFile->getMimeType($sExt);

        $sBase64 = base64_encode(file_get_contents($sFile));

        return "data:$sMimeType;base64,$sBase64";
    }

    /**
     * Scan the path ($sDir) of all file-references found.
     * Note: This function is a slightly modified version from Christian Schepp Schaefer's function (CSS JS booster).
     *
     * @param string $sFile Contents to scan.
     * @param string $sDir Folder name to prepend.
     *
     * @return string Content with adjusted paths.
     */
    public static function cssDataUriCleanup($sFile, $sDir)
    {
        // Scan any left file references & adjust their paths
        preg_match_all(self::REGEX_CSS_IMPORT_URL_PATTERN, $sFile, $aHit, PREG_PATTERN_ORDER);

        for ($i = 0, $iCountHit = count($aHit[0]); $i < $iCountHit; $i++) {
            $sProtocolContext = str_replace(['"', "'"], '', $aHit[2][$i]);
            if (
                substr($sProtocolContext, 0, 5) !== 'http:' &&
                substr($sProtocolContext, 0, 6) !== 'https:' &&
                substr($sProtocolContext, 0, 5) !== 'data:' &&
                substr($sProtocolContext, 0, 6) !== 'mhtml:' &&
                substr($sProtocolContext, 0, 1) !== '/' &&
                substr($sProtocolContext, strlen($sProtocolContext) - 4, 4) !== '.htc'
            ) {
                $sSearch = $aHit[1][$i] . $aHit[2][$i] . $aHit[3][$i];
                $sReplace = $sDir . $aHit[1][$i];
                $sReplace .= $aHit[2][$i] . $aHit[3][$i];

                $sFile = str_replace($sSearch, $sReplace, $sFile);
            }
        }

        return $sFile;
    }
}
