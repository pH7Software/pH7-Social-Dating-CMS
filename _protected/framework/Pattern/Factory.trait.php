<?php
/**
 * @title            Factory Trait
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Pattern
 * @version          1.0
 */

namespace PH7\Framework\Pattern;

defined('PH7') or exit('Restricted access');

trait Factory
{
    use Statik;

    /**
     * Loading a class.
     *
     * @access public
     * @static
     * @return object Return the instance of the class.
     * @throws \PH7\Framework\Error\CException\PH7RuntimeException If the class is not found or if it has not been defined.
     */
    public static function load(...$aArgs)
    {
        $sClass = static::class;

        if (class_exists($sClass)) {
            return (new \ReflectionClass($sClass))->newInstanceArgs($aArgs);
        } else {
            throw new \PH7\Framework\Error\CException\PH7RuntimeException(
                'The "' . $sClass . '" was not found or is not defined.'
            );
        }
    }
}
