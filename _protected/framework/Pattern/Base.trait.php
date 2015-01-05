<?php
/**
 * @title            Base Pattern Trait
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Pattern
 */

namespace PH7\Framework\Pattern;
defined('PH7') or exit('Restricted access');

trait Base
{

    /**
     * Class constructor.
     * Marked as private so this constructor cannot be called from outside.
     *
     * @access private
     * @final
     */
    final private function __construct() {}

    /**
     * Block cloning.
     *
     * @access private
     * @final
     */
    final private function __clone() {}

}

