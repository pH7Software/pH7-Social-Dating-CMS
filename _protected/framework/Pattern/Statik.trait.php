<?php
/**
 * @title            Statik Pattern Trait
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2016-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Pattern
 */

namespace PH7\Framework\Pattern;

defined('PH7') or exit('Restricted access');

/**
 * "Statik" and not "Static" because "static" is a reseved PHP keyword.
 */
trait Statik
{
    /**
     * Private Constructor & Cloning to prevent direct creation of object and blocking cloning.
     */
    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
