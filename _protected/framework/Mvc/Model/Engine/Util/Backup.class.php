<?php
/**
 * @title            Backup (Database) Class
 * @desc             Backs up the database.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 * @version          1.3
 * @history          04/13/2014 - We replaced the bzip2 compression program by gzip because bzip2 is much too slow to compress and uncompress files and the  compression is only a little higher. In addition, gzip is much more common on shared hosting that bzip2.
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Core\Kernel,
PH7\Framework\Config\Config,
PH7\Framework\Date\CDateTime,
PH7\Framework\Navigation\Browser,
PH7\Framework\Mvc\Model\Engine\Db;

class Backup
{
    private $_sPathName, $_sSql;

    /**
     * Constructor.
     *
     * @access public
     * @param string $sPathName Can be null for showing the data only ( by using Backup->back()->show() ). Default NULL
     */
    public function __construct($sPathName = null)
    {
        $this->_sPathName = $sPathName;
    }

    /**
     * Makes a SQL contents backup.
     *
     * @access public
     * @return object this
     */
    public function back()
    {
        $this->_sSql =
        "#################### Database Backup ####################\n" .
        '# ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ', Build ' . Kernel::SOFTWARE_BUILD . "\r\n" .
        '# Database name: ' . Config::getInstance()->values['database']['name'] . "\r\n" .
        '# Created on ' . (new CDateTime)->get()->dateTime() . "\r\n" .
        "#########################################################\r\n\r\n";

        $aTables = $aColumns = $aValues = array();
        $oAllTables = Db::showTables();
        while ($aRow = $oAllTables->fetch()) $aTables[] = $aRow[0];
        unset($oAllTables);

        $oDb = Db::getInstance();

        // Loop through tables
        foreach ($aTables as $sTable)
        {
            $oResult = $oDb->query('SHOW CREATE TABLE ' . $sTable);

            $iNum = (int) $oResult->rowCount();

            if ($iNum > 0)
            {
                $aRow = $oResult->fetch();

                $this->_sSql .= "#\n# Table: $sTable\r\n#\r\n\r\n";
                $this->_sSql .= "DROP TABLE IF EXISTS $sTable;\r\n\r\n";

                $sValue = $aRow[1];

                /*** Clean up statement ***/
                $sValue = str_replace('`', '', $sValue);

                /*** Table structure ***/
                $this->_sSql .= $sValue . ";\r\n\r\n";

                unset($aRow);
            }
            unset($oResult);

            $oResult = $oDb->query('SELECT * FROM ' . $sTable);

            $iNum = (int) $oResult->rowCount();

            if ($iNum > 0)
            {
                while ($aRow = $oResult->fetch())
                {
                    foreach ($aRow as $sColumn => $sValue)
                    {
                        if (!is_numeric($sColumn))
                        {
                            if (!is_numeric($sValue) && !empty($sValue))
                                $sValue = Db::getInstance()->quote($sValue);

                            $sValue = str_replace(array("\r", "\n"), array('', '\n'), $sValue);

                            $aColumns[] = $sColumn;
                            $aValues[] = $sValue;
                        }
                    }

                    $this->_sSql .= 'INSERT INTO ' . $sTable . ' (' . implode(', ', $aColumns) . ') VALUES(\'' . implode('\', \'', $aValues) . "');\n";

                    unset($aColumns, $aValues);
                }
                $this->_sSql .= "\r\n\r\n";

                unset($aRow);
            }
            unset($oResult);
        }
        unset($oDb);

        return $this;
    }

    /**
     * Gets the SQL contents.
     *
     * @access public
     * @return string
     */
    public function show()
    {
        return $this->_sSql;
    }

    /**
     * Saves the backup in the server.
     *
     * @access public
     * @return void
     */
    public function save()
    {
         $rHandle = fopen($this->_sPathName, 'wb');
         fwrite($rHandle, $this->_sSql);
         fclose($rHandle);
    }

    /**
     * Saves the backup in the gzip compressed archive in the server.
     *
     * @access public
     * @return void
     */
    public function saveArchive()
    {
        $rArchive = gzopen($this->_sPathName, 'w');
        gzwrite($rArchive, $this->_sSql);
        gzclose($rArchive);
    }

    /**
     * Restore SQL backup file.
     *
     * @access public
     * @return mixed (boolean | string) Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restore()
    {
        $mRet = Various::execQueryFile($this->_sPathName);
        return ($mRet !== true) ? print_r($mRet, true) : true;
    }

    /**
     * Restore the gzip compressed archive backup.
     *
     * @access public
     * @return mixed (boolean | string) Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restoreArchive()
    {
        $rArchive = gzopen($this->_sPathName, 'r');

        $sSqlContent = '';
        while (!feof($rArchive))
            $sSqlContent .= gzread($rArchive, filesize($this->_sPathName));

        gzclose($rArchive);

        $sSqlContent = str_replace(PH7_TABLE_PREFIX,  Db::prefix(), $sSqlContent);
        $oDb = Db::getInstance()->exec($sSqlContent);
        unset($sSqlContent);

        return ($oDb === false) ? print_r($oDb->errorInfo(), true) : true;
    }

    /**
     * Download the backup on the desktop.
     *
     * @access public
     * @return void
     */
    public function download()
    {
        $this->_download();
    }

    /**
     * Download the backup in the gzip compressed archive on the desktop.
     *
     * @access public
     * @return void
     */
    public function downloadArchive()
    {
        $this->_download(true);
    }

    /**
     * Generic method that allows you to download a file or a SQL file gzip compressed archive.
     *
     * @access private
     * @param boolean $bArchive If TRUE, the string will be compressed in gzip. Default FALSE
     * @return void
     */
    private function _download($bArchive = false)
    {
        ob_start();
        /***** Set Headers *****/
        (new Browser)->nocache(); // No cache
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->_sPathName);

        /***** Show the SQL contents *****/
        echo ($bArchive ? gzencode($this->_sSql, 9, FORCE_GZIP) : $this->_sSql);

        /***** Catch output *****/
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }
}
