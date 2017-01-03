<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             We define functions (and helpers).
 *                   Predefined functions can save considerable resources and speeds up the code with respect to functions in variables assigned by through the object's template engine (PH7Tpl).
 *
 * @updated          Last Update 09/11/13 03:44
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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
     * @return object this
     */
    public function assign()
    {
        $this->addFunc('<ph:date value="(\w+)" ?/?>', 'date(\'$1\')');

        return $this;
    }

}
