<?php
/**
 * @title            Model Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 * @version          0.6
 */

namespace PH7\Framework\Mvc\Model\Engine;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache, PH7\Framework\File\File;

abstract class Model extends Entity
{

    const SQL_FILE_EXT = '.sql';

    protected $orm, $cache;
    private $_sContents;

    public function __construct()
    {
        $this->orm = Record::getInstance();
        $this->cache = new Cache;
    }

    /**
     * @param string $sFile SQL file name.
     * @param string $sPath Path to SQL file.
     * @param array $aParams Default NULL
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function exec($sFile, $sPath, array $aParams = null)
    {
        $rStmt = Db::getInstance()->prepare( $this->getQuery($sFile, $sPath) );
        $bRet = $rStmt->execute($aParams);
        Db::free($rStmt);
        return $bRet;
    }

    /**
     * Get SQL query file.
     *
     * @param string $sFile SQL file name.
     * @param string $sPath Path to SQL file.
     * @return string The SQL query.
     */
    public function getQuery($sFile, $sPath)
    {
        $sFullPath = $sPath . $sFile . static::SQL_FILE_EXT;
        $this->_sContents = (new File)->getFile($sFullPath);
        $this->_parseVar();

        return $this->_sContents;
    }

    /**
     * Parse query variables.
     *
     * @return void
     */
    private function _parseVar()
    {
        $this->_sContents = str_replace('[DB_PREFIX]', Db::prefix(), $this->_sContents);
    }

    public function __destruct()
    {
        unset($this->orm, $this->cache);
    }

}
