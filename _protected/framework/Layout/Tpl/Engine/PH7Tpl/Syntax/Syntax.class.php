<?php
/***************************************************************************
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

defined('PH7') or exit('Restricted access');

abstract class Syntax
{
    /** @var string */
    protected $sCode;

    /** @var string */
    protected $sTplFile;

    /**
     * Parse pH7Tpl's language syntax.
     *
     * @return void
     */
    abstract public function parse();

    /**
     * Get the converted PHP code from template engine's syntax.
     *
     * @return string
     */
    public function get()
    {
        return $this->sCode;
    }

    /**
     * Set the template contents.
     *
     * @param string $sCode
     */
    public function set($sCode)
    {
        $this->sCode = $sCode;
    }
}
