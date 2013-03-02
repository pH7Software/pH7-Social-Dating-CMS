<?php
/**
 * @title            Backup (Database) Class
 * @desc             Backs up the database.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 * @version          1.1
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel, PH7\Framework\Date\CDateTime, PH7\Framework\Mvc\Model\Engine\Db;

class Backup
{

    private $_sPathName, $_sSql;

    /**
     * Constructor.
     *
     * @access public
     * @param string $sPathName Default NULL
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
        '# ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ', Build ' . Kernel::SOFTWARE_BUILD . "\n" .
        '# Database name: ' . \PH7\Framework\Config\Config::getInstance()->values['database']['name'] . "\n" .
        '# Created on ' . (new CDateTime)->get()->dateTime() . "\n" .
        "#########################################################\n\n";

        $aTables = array();
        $oAllTables = Db::showTables();
        while ($aRow = $oAllTables->fetch()) $aTables[] = $aRow[0];
        unset($oAllTables);

        $rStmt = Db::getInstance();

        // Loop through tables
        foreach ($aTables as $sTable)
        {
            $oResult = $rStmt->query('SHOW CREATE TABLE ' . $sTable);
            Db::free();

            $iNum = (int) $oResult->rowCount();

            if ($iNum > 0)
            {
                $aRow = $oResult->fetch();

                $this->_sSql .= "#\n# Table: $sTable\n#\n\n";
                $this->_sSql .= "DROP TABLE IF EXISTS $sTable;\n\n";

                $sValue = $aRow[1];

                /*** Clean up statement ***/
                $sValue = str_replace('`', '', $sValue);

                /*** Table structure ***/
                $this->_sSql .= $sValue . ";\n\n";

                unset($aRow);
            }
            unset($oResult);

            $oResult = $rStmt->query('SELECT * FROM ' . $sTable);
            Db::free();

            $iNum = (int) $oResult->rowCount();

            if ($iNum > 0)
            {
                while ($aRow = $oResult->fetch())
                {
                    foreach ($aRow as $sColumn => $sValue)
                    {
                        if (!is_numeric($sColumn))
                        {
                            $sValue = str_replace("\r", '', $sValue);
                            $sValue = str_replace("\n", '\n', $sValue);

                            $aColumns[] = $sColumn;
                            $aValues[] = $sValue;
                        }
                    }

                    $this->_sSql .= 'INSERT INTO ' . $sTable . ' (' . implode(', ', $aColumns) . ') VALUES(\'' . implode('\', \'', $aValues) . "');\n";

                    unset($aColumns, $aValues);

                    $this->_sSql .= "\n\n";
                }
                unset($aRow);
            }
            unset($oResult);
        }

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
     * Saves the backup in the bzip2 compressed archive in the server.
     *
     * @access public
     * @return void
     */
    public function saveArchive()
    {
        $rArchive = bzopen($this->_sPathName, 'w');
        bzwrite($rArchive, $this->_sSql);
        bzclose($rArchive);
    }

    /**
     * Restore SQL backup file.
     *
     * @access public
     * @return mixed (boolean | string) Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restore()
    {
        $mRet = Various::execFileQuery($this->_sPathName);
        return ($mRet !== true) ? print_r($mRet, true) : true;
    }

    /**
     * Restore the bzip2 compressed archive backup.
     *
     * @access public
     * @return mixed (boolean | string) Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restoreArchive()
    {
        $rArchive = bzopen($this->_sPathName, 'r');

        $sSqlContent = '';
        while (!feof($rArchive))
            $sSqlContent .= bzread($rArchive, filesize($this->_sPathName));

        bzclose($rArchive);

        $sSqlContent = str_replace(PH7_TABLE_PREFIX,  Db::prefix(), $sSqlContent);
        $rStmt = Db::getInstance()->exec($sSqlContent);
        unset($sSqlContent);

        return ($rStmt === false) ? print_r($rStmt->errorInfo(), true) : true;
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
     * Download the backup in the bzip2 compressed archive on the desktop.
     *
     * @access public
     * @return void
     */
    public function downloadArchive()
    {
        $this->_download(true);
    }

    /**
     * Generic method that allows you to download a file or a SQL file bzip2 compressed archive.
     *
     * @access private
     * @param boolean $bArchive If TRUE, the string will be compressed in bzip2. Default FALSE
     * @return void
     */
    private function _download($bArchive = false)
    {
        ob_start();
        /***** Set Headers *****/
        (new \PH7\Framework\Navigation\Browser)->nocache(); // No cache
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->_sPathName);

        /***** Show the SQL contents *****/
        echo ($bArchive ? bzcompress($this->_sSql, 9) : $this->_sSql);

        /***** Catch output *****/
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }

}
