<?php
/**
 * @title            Factory Trait
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Pattern
 */

namespace PH7\Framework\Pattern;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7RuntimeException;
use ReflectionClass;

trait Factory
{
    use Statik;

    /**
     * Loading a class.
     *
     * @return object Return the instance of the class.
     *
     * @throws PH7RuntimeException If the class is not found or if it has not been defined.
     * @throws \ReflectionException If the class doesn't exist.
     */
    public static function load(...$aArgs)
    {
        $sClass = static::class;

        if (class_exists($sClass)) {
            return (new ReflectionClass($sClass))->newInstanceArgs($aArgs);
        }

        throw new PH7RuntimeException(
            sprintf('"%s" class was not found or is not defined.', $sClass)
        );
    }
}
