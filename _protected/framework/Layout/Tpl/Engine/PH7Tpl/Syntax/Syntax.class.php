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
     * @return void
     */
    abstract public function parse();

    /**
     * @return string
     */
    public function get()
    {
        return $this->sCode;
    }

    /**
     * @param string $sCode
     */
    public function set($sCode)
    {
        $this->sCode = $sCode;
    }
}
