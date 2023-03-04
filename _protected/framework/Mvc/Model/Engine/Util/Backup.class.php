<?php
/**
 * @title            Backup (Database) Class
 * @desc             Backs up the database.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 * @version          1.3
 * @history          04/13/2014 - Replaced the bzip2 compression program by gzip because bzip2 is much too slow to compress and uncompress files and the compression is only a little higher.
 *                   In addition, gzip is much more common on shared hosting that bzip2.
 */

declare(strict_types=1);

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
    public const SQL_FILE_EXT = 'sql';
    public const ARCHIVE_FILE_EXT = 'gz';

    private const GZIP_COMPRESS_LEVEL = 9;

    private ?string $sPath;

    private string $sSql;

    /**
     * @param string|null $sFullPath Can be null for showing the data only ( by using Backup->back()->show() ).
     */
    public function __construct(?string $sFullPath = null)
    {
        $this->sPath = $sFullPath;
    }

    /**
     * Makes a SQL contents backup.
     */
    public function back(): self
    {
        $this->sSql = $this->getHeaderContents();

        $aTables = $aColumns = $aValues = [];
        $oAllTables = Db::showTables();
        while ($aRow = $oAllTables->fetch()) {
            $aTables[] = $aRow[0];
        }
        unset($oAllTables);

        $oDb = Db::getInstance();

        // Loop through tables
        foreach ($aTables as $sTable) {
            $rResult = $oDb->query('SHOW CREATE TABLE ' . $sTable);

            $iNum = $rResult->rowCount();
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

            $iNum = $rResult->rowCount();
            if ($iNum > 0) {
                while ($aRow = $rResult->fetch()) {
                    foreach ($aRow as $sColumn => $sValue) {
                        if (!is_numeric($sColumn)) {
                            if (!empty($sValue) && is_string($sValue)) {
                                $sValue = Db::getInstance()->quote($sValue);
                                $sValue = str_replace(["\r", "\n"], ['', '\n'], $sValue);
                            }

                            $aColumns[] = $sColumn;
                            $aValues[] = $sValue;
                        }
                    }

                    $this->sSql .= 'INSERT INTO ' . $sTable . ' (' . implode(', ', $aColumns) . ') VALUES(\'' . implode(
                            '\', \'',
                            $aValues
                        ) . "');\n";

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
     */
    public function show(): string
    {
        return $this->sSql;
    }

    /**
     * Saves the backup in the server.
     */
    public function save(): void
    {
        $rHandle = fopen($this->sPath, 'wb');
        fwrite($rHandle, $this->sSql);
        fclose($rHandle);
    }

    /**
     * Saves the backup in the gzip compressed archive in the server.
     */
    public function saveArchive(): void
    {
        $rArchive = gzopen($this->sPath, 'w');
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
        $mRet = Various::execQueryFile($this->sPath);
        return $mRet !== true ? print_r($mRet, true) : true;
    }

    /**
     * Restore the gzip compressed archive backup.
     *
     * @return bool|string Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restoreArchive()
    {
        $rArchive = gzopen($this->sPath, 'r');

        $sSqlContent = '';
        while (!feof($rArchive)) {
            $sSqlContent .= gzread($rArchive, filesize($this->sPath));
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
     */
    public function download(): void
    {
        $this->downloadBackup();
    }

    /**
     * Download the backup in the gzip compressed archive on the desktop.
     */
    public function downloadArchive(): void
    {
        $this->downloadBackup(true);
    }

    /**
     * Returns the SQL header containing useful information relative to the backup.
     */
    public function getHeaderContents(): string
    {
        $sSql = "-- Database Backup\n" .
            '-- ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ', Build ' . Kernel::SOFTWARE_BUILD . "\r\n" .
            '-- Database name: ' . Config::getInstance()->values['database']['name'] . "\r\n" .
            '-- Created on ' . (new CDateTime)->get()->dateTime() . "\r\n" .
            "--\r\n\r\n";

        return $sSql;
    }

    /**
     * Generic method that allows you to download a file or a SQL gzip file compressed archive.
     *
     * @param bool $bArchive If TRUE, the string will be compressed in gzip.
     *
     * @return void
     */
    private function downloadBackup(bool $bArchive = false): void
    {
        ob_start();
        /***** Set Headers *****/
        (new Browser)->noCache(); // No cache
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->sPath);

        /***** Show the SQL contents *****/
        echo $bArchive ? gzencode($this->sSql, self::GZIP_COMPRESS_LEVEL, FORCE_GZIP) : $this->sSql;

        /***** Catch output *****/
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }
}
