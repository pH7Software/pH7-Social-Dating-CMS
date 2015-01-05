<?php
/**
 * @title            Singleton Helper Trait
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Pattern
 * @version          1.0
 */

namespace PH7\Framework\Pattern;
defined('PH7') or exit('Restricted access');

trait Singleton
{

    use Base;

    /**
     * @staticvar object $_oInstance
     */
    protected static $_oInstance = null;

    /**
     * Get instance of class.
     *
     * @access public
     * @static
     * @return object Return the instance class or create intitial instance of the class.
     */
    public static function getInstance()
    {
        return (null === static::$_oInstance) ? static::$_oInstance = new static : static::$_oInstance;
    }

}

