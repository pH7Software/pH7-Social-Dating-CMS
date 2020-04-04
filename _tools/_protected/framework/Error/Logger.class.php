<?php
/**
 * @title          Logger Class
 * @desc           Handler Logger Management.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error
 * @version        1.0
 */

namespace PH7\Framework\Error;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Core;
use PH7\Framework\File\File;

class Logger extends Core
{
    const LOG_DIR = 'pH7log/';
    const EXCEPT_DIR = 'except/';
    const GZIP_DIR = 'gzip/';
    const EXT = '.log';

    const FILE_MESSAGE_TYPE = 3;

    /** @var string */
    protected $sDir;

    /** @var string */
    protected $sFileName;

    public function __construct()
    {
        $this->sDir = PH7_PATH_LOG . static::LOG_DIR;
        $this->sFileName = 'pH7log-' . date('d_m_Y');

        parent::__construct();
    }

    public function msg($sMsg)
    {
        error_log(
            $sMsg . File::EOL,
            self::FILE_MESSAGE_TYPE,
            $this->sDir . $this->sFileName . self::EXT
        );
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
