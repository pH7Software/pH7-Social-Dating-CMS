<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Define functions (and helpers).
 *                   Predefined functions can save considerable resources and speeds up the code with respect to functions in variables assigned through the object's template engine (PH7Tpl).
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

defined('PH7') or exit('Restricted access');

class Func extends Predefined
{
    /**
     * {@inheritdoc}
     */
    public function assign()
    {
        $this->dataFunction();

        return $this;
    }

    private function dataFunction()
    {
        $this->addFunc(
            '<ph:date value="([\w\s\-/\|\.,:\\\\]+)" ?/?>',
            'date(\'$1\')'
        );
    }
}
