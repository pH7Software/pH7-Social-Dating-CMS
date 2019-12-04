<?php
/**
 * @title            Model Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 */

namespace PH7\Framework\Mvc\Model\Engine;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\File\File;

abstract class Model extends Entity
{
    const DB_PREFIX_FLAG = '[DB_PREFIX]';
    const SQL_FILE_EXT = '.sql';

    /** @var Record */
    protected $orm;

    /** @var Cache */
    protected $cache;

    /** @var string */
    private $sContents;

    public function __construct()
    {
        $this->orm = Record::getInstance();
        $this->cache = new Cache;
    }

    /**
     * @param string $sFile SQL file name.
     * @param string $sPath Path to SQL file.
     * @param array $aParams
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function exec($sFile, $sPath, array $aParams = null)
    {
        $rStmt = Db::getInstance()->prepare($this->getQuery($sFile, $sPath));
        $bRet = $rStmt->execute($aParams);
        Db::free($rStmt);

        return $bRet;
    }

    /**
     * Get SQL query file.
     *
     * @param string $sFile SQL file name.
     * @param string $sPath Path to SQL file.
     *
     * @return string The SQL query.
     */
    public function getQuery($sFile, $sPath)
    {
        $sFullPath = $sPath . $sFile . static::SQL_FILE_EXT;
        $this->sContents = (new File)->getFile($sFullPath);
        $this->parseVar();

        return $this->sContents;
    }

    /**
     * Parse query variables.
     *
     * @return void
     */
    private function parseVar()
    {
        $this->sContents = str_replace(
            self::DB_PREFIX_FLAG,
            Db::prefix(),
            $this->sContents
        );
    }
}
