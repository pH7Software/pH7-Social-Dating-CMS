<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             We define functions (and helpers).
 *                   Predefined functions can save considerable resources and speeds up the code with respect to functions in variables assigned by through the object's template engine (PH7Tpl).
 *
 * @updated          The Last Update 08/26/13 21:16 (Greenwich Mean Time)
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @version          1.0.2
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;
defined('PH7') or exit('Restricted access');

class Func extends Predefined
{

    /**
     * Assign the global functions.
     *
     * @access public
     * @return this object
     */
    public function assign()
    {
        $this->addFunc('<ph:date value="(\w+)" ?/?>', 'date(\'$1\')');

        return $this;
    }

}
