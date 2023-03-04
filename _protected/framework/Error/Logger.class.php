<?php
/**
 * @desc           Handler Logger Management.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7/ Framework / Error
 * @version        1.2
 */

declare(strict_types=1);

namespace PH7\Framework\Error;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Core;
use PH7\Framework\File\File;

class Logger extends Core
{
    protected const LOG_DIR = 'pH7log/';
    protected const EXCEPT_DIR = 'except/';
    protected const GZIP_DIR = 'gzip/';
    protected const EXT = '.log';

    private const FILE_MESSAGE_TYPE = 3;

    protected string $sDir;

    protected string $sFileName;

    public function __construct()
    {
        $this->sDir = PH7_PATH_LOG . static::LOG_DIR;
        $this->sFileName = 'pH7log-' . date('d_m_Y');

        parent::__construct();
    }

    public function msg(string $sMsg): void
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
