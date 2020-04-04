<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Config
 */

namespace PH7\Framework\Config;

// The prototypes of the methods
interface Configurable
{
    /**
     * Load ini file.
     *
     * @param string $sFile
     *
     * @return bool Returns FALSE if the file doesn't exist, TRUE otherwise.
     */
    public function load($sFile);

    /**
     * Get a config option by key.
     *
     * @param string $sKey The configuration setting key.
     *
     * @return string
     */
    public function getValue($sKey);

    /**
     * Set dynamically a value to config data.
     *
     * @param string $sKey A unique config key.
     * @param string $sValue The value to add.
     *
     * @return void
     */
    public function setValue($sKey, $sValue);
}
