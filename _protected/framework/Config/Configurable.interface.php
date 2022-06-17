<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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
    public function load(string $sFile): bool;

    /**
     * Get a config option by key.
     *
     * @param string $sKey The configuration setting key.
     *
     * @return string
     */
    public function getValue(string $sKey): string;

    /**
     * Set dynamically a value to config data.
     *
     * @param string $sKey A unique config key.
     * @param string $sValue The value to add.
     *
     * @return void
     */
    public function setValue(string $sKey, string $sValue): void;
}
