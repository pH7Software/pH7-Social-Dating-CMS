<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Server
 */

namespace PH7\Framework\Server;

defined('PH7') or exit('Restricted access');

class Environment
{
    const ENV_FILE_EXT = '.env';

    const PRODUCTION_MODE = 'production';
    const DEVELOPMENT_MODE = 'development';

    const MODES = [
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
