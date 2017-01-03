<?php
/**
 * @title            Store Stats Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Analytics
 * @version          0.9
 */

namespace PH7\Framework\Analytics;
defined('PH7') or exit('Restricted access');

class StoreStats
{

    const DIR = 'stats/', EXT = '.txt';

    /**
     * Read cache.
     *
     * @return array Cache data.
     * @throws \PH7\Framework\Cache\Exception If the file cannot be gotten.
     */
    protected function read($sFileName)
    {
        $sFullPath = PH7_PATH_TMP . static::DIR . $sFileName . static::EXT;

        if (!$aGetData = @file_get_contents($sFullPath))
            throw new \PH7\Framework\Cache\Exception('Couldn\'t get cache file: \'' . $sFullPath . '\'');

        $aData = (!empty($aGetData)) ? unserialize($aGetData) : array();

        return $aData;
    }

    /**
     * Save cache.
     *
     * @param string $sFileName
     * @param string $sContents
     * @return void
     * @throws \PH7\Framework\Cache\Exception If the file cannot be written.
     */
    protected function save($sFileName, $sContents)
    {
        $sFullPath = PH7_PATH_TMP . static::DIR . $sFileName . static::EXT;
        $sExceptMsg = 'Couldn\'t write cache file: \'' . $sFullPath . '\'';
        $aData = array();

        if (!is_file($sFullPath))
        {
            if (!@file_put_contents($sFullPath, serialize($aData)))
                throw new \PH7\Framework\Cache\Exception($sExceptMsg);
        }
        else
        {
            $aLine = file($sFullPath);
            $aData = unserialize($aLine[0]);
            $sContents = strtolower($sContents); // Case-insensitive

            if (!empty($aData[$sContents]))
                $aData[$sContents]++;
            else
                $aData[$sContents] = 1;

            if (!@file_put_contents($sFullPath, serialize($aData), FILE_APPEND))
                throw new \PH7\Framework\Cache\Exception($sExceptMsg);
        }
    }

}
