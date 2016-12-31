<?php
/**
 * @title            Singleton Helper Trait
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Pattern
 * @version          1.0
 */

namespace PH7\Framework\Pattern;

defined('PH7') or exit('Restricted access');

trait Singleton
{
    use Statik;

    /**
     * @staticvar object $_oInstance
     */
    protected static $_oInstance = null;

    /**
     * Get instance of class.
     *
     * @access public
     * @static
     * @return object Returns the instance class or create initial instance of the class.
     */
    public static function getInstance()
    {
        return (null === static::$_oInstance) ? static::$_oInstance = new static : static::$_oInstance;
    }

    /**
     * Directly call "static::getInstance()" method when the object is called as a function.
     */
    public function __invoke()
    {
        return static::getInstance();
    }

    /**
     * Private serialize/unserialize method to prevent serializing/unserializing.
     */
    private function __wakeup() {}
    private function __sleep() {}
}
