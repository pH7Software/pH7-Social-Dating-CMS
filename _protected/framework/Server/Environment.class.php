<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Framework / Server
 */

namespace PH7\Framework\Server;

defined('PH7') or exit('Restricted access');

class Environment
{
    private const ENV_FILE_EXT = '.env';

    public const PRODUCTION_MODE = 'production';
    public const DEVELOPMENT_MODE = 'development';

    private const MODES = [
        self::PRODUCTION_MODE,
        self::DEVELOPMENT_MODE
    ];

    /**
     * @param string $sEnvName The chosen environment name.
     *
     * @return string The correct config environment filename (without .php ext).
     */
    public static function getFileName($sEnvName)
    {
        $sFileName = in_array($sEnvName, self::MODES, true) ? $sEnvName : self::PRODUCTION_MODE;

        return $sFileName . self::ENV_FILE_EXT;
    }
}
