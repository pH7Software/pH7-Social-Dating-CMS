<?php
/**
 * @title            Backup (Database) Class
 * @desc             Backs up the database.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 * @version          1.3
 * @history          04/13/2014 - Replaced the bzip2 compression program by gzip because bzip2 is much too slow to compress and uncompress files and the compression is only a little higher.
 *                   In addition, gzip is much more common on shared hosting that bzip2.
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\File\GenerableFile;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Navigation\Browser;

class Backup implements GenerableFile
{
    const SQL_FILE_EXT = 'sql';
    const ARCHIVE_FILE_EXT = 'gz';
    const GZIP_COMPRESS_LEVEL = 9;

    /** @var string */
    private $sPathName;

    /** @var string */
    private $sSql;

    /**
     * @param string $sPathName Can be null for showing the data only ( by using Backup->back()->show() ).
     */
    public function __construct($sPathName = null)
    {
        $this->sPathName = $sPathName;
    }

    /**
     * Makes a SQL contents backup.
     *
     * @return self
     */
    public function back()
    {
        $this->sSql = $this->getHeaderContents();

        $aTables = $aColumns = $aValues = [];
        $oAllTables = Db::showTables();
        while ($aRow = $oAllTables->fetch()) $aTables[] = $aRow[0];
        unset($oAllTables);

        $oDb = Db::getInstance();

        // Loop through tables
        foreach ($aTables as $sTable) {
            $rResult = $oDb->query('SHOW CREATE TABLE ' . $sTable);

            $iNum = (int)$rResult->rowCount();

            if ($iNum > 0) {
                $aRow = $rResult->fetch();

                $this->sSql .= "--\r\n-- Table: $sTable\r\n--\r\n\r\n";
                $this->sSql .= "DROP TABLE IF EXISTS $sTable;\r\n\r\n";

                $sValue = $aRow[1];

                /*** Clean up statement ***/
                $sValue = str_replace('`', '', $sValue);

                /*** Table structure ***/
                $this->sSql .= $sValue . ";\r\n\r\n";

                unset($aRow);
            }
            unset($rResult);

            $rResult = $oDb->query('SELECT * FROM ' . $sTable);

            $iNum = (int)$rResult->rowCount();

            if ($iNum > 0) {
                while ($aRow = $rResult->fetch()) {
                    foreach ($aRow as $sColumn => $sValue) {
                        if (!is_numeric($sColumn)) {
                            if (!empty($sValue) && !is_numeric($sValue)) {
                                $sValue = Db::getInstance()->quote($sValue);
                            }

                            $sValue = str_replace(["\r", "\n"], ['', '\n'], $sValue);

                            $aColumns[] = $sColumn;
                            $aValues[] = $sValue;
                        }
                    }

                    $this->sSql .= 'INSERT INTO ' . $sTable . ' (' . implode(', ', $aColumns) . ') VALUES(\'' . implode('\', \'', $aValues) . "');\n";

                    unset($aColumns, $aValues);
                }
                $this->sSql .= "\r\n\r\n";

                unset($aRow);
            }
            unset($rResult);
        }
        unset($oDb);

        return $this;
    }

    /**
     * Gets the SQL contents.
     *
     * @return string
     */
    public function show()
    {
        return $this->sSql;
    }

    /**
     * Saves the backup in the server.
     *
     * @return void
     */
    public function save()
    {
        $rHandle = fopen($this->sPathName, 'wb');
        fwrite($rHandle, $this->sSql);
        fclose($rHandle);
    }

    /**
     * Saves the backup in the gzip compressed archive in the server.
     *
     * @return void
     */
    public function saveArchive()
    {
        $rArchive = gzopen($this->sPathName, 'w');
        gzwrite($rArchive, $this->sSql);
        gzclose($rArchive);
    }

    /**
     * Restore SQL backup file.
     *
     * @return bool|string Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restore()
    {
        $mRet = Various::execQueryFile($this->sPathName);
        return $mRet !== true ? print_r($mRet, true) : true;
    }

    /**
     * Restore the gzip compressed archive backup.
     *
     * @return bool|string Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restoreArchive()
    {
        $rArchive = gzopen($this->sPathName, 'r');

        $sSqlContent = '';
        while (!feof($rArchive)) {
            $sSqlContent .= gzread($rArchive, filesize($this->sPathName));
        }

        gzclose($rArchive);

        $sSqlContent = str_replace(PH7_TABLE_PREFIX, Db::prefix(), $sSqlContent);
        $oDb = Db::getInstance();
        $rStmt = $oDb->exec($sSqlContent);
        unset($sSqlContent);

        return $rStmt === false ? print_r($oDb->errorInfo(), true) : true;
    }

    /**
     * Download the backup on the desktop.
     *
     * @return void
     */
    public function download()
    {
        $this->downloadBackup();
    }

    /**
     * Download the backup in the gzip compressed archive on the desktop.
     *
     * @return void
     */
    public function downloadArchive()
    {
        $this->downloadBackup(true);
    }

    /**
     * Returns the SQL header containing useful information relative to the backup.
     *
     * @return string
     */
    public function getHeaderContents()
    {
        $sSql = "-- Database Backup\n" .
            '-- ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ', Build ' . Kernel::SOFTWARE_BUILD . "\r\n" .
            '-- Database name: ' . Config::getInstance()->values['database']['name'] . "\r\n" .
            '-- Created on ' . (new CDateTime)->get()->dateTime() . "\r\n" .
            "--r\n\r\n";

        return $sSql;
    }

    /**
     * Generic method that allows you to download a file or a SQL gzip file compressed archive.
     *
     * @param bool $bArchive If TRUE, the string will be compressed in gzip.
     *
     * @return void
     */
    private function downloadBackup($bArchive = false)
    {
        ob_start();
        /***** Set Headers *****/
        (new Browser)->noCache(); // No cache
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->sPathName);

        /***** Show the SQL contents *****/
        echo $bArchive ? gzencode($this->sSql, self::GZIP_COMPRESS_LEVEL, FORCE_GZIP) : $this->sSql;

        /***** Catch output *****/
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }
}
