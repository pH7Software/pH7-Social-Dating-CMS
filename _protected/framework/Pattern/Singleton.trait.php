<?php
/**
 * @title            Singleton Helper Trait
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Pattern
 */

namespace PH7\Framework\Pattern;

defined('PH7') or exit('Restricted access');

trait Singleton
{
    use Statik;

    /**
     * @staticvar object $oInstance
     */
    protected static $oInstance = null;

    /**
     * Get instance of class.
     *
     * @return self Returns the instance class or create initial instance of the class.
     */
    public static function getInstance()
    {
        return null === static::$oInstance ? static::$oInstance = new static : static::$oInstance;
    }

    /**
     * Directly call "static::getInstance()" method when the object is called as a function.
     *
     * @return static
     */
    public function __invoke()
    {
        return static::getInstance();
    }
}
