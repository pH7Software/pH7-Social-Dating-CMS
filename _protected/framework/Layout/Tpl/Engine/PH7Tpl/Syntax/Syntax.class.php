<?php
/***************************************************************************
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

abstract class Syntax
{
    /** @var string */
    protected $sCode;

    /**
     * Parse pH7Tpl's language syntax.
     *
     * @param string $sCode
     *
     * @return void
     */
    abstract public function parse($sCode);

    /**
     * @return string
     */
    public function get()
    {
        return $this->sCode;
    }
}
