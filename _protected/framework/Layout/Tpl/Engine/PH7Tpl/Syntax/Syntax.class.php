<?php
/***************************************************************************
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
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
     *
     * @throws EmptyCodeException
     */
    abstract public function parse();

    /**
     * Get the converted PHP code from template engine's syntax.
     *
     * @return string
     */
    public function getParsedCode()
    {
        return $this->sCode;
    }

    /**
     * Set the template contents.
     *
     * @param string $sCode
     *
     * @return void
     */
    public function setCode($sCode)
    {
        $this->sCode = $sCode;
    }

    /**
     * @return void
     */
    public function setShortcutsToObjects()
    {
        $this->sCode = str_replace(
            [
                '$browser->',
                '$registry->',
                '$str->',
                '$config->'
            ],
            [
                '$this->browser->',
                '$this->registry->',
                '$this->str->',
                '$this->config->'
            ],
            $this->sCode
        );
    }

    /**
     * @param string $sTplFile
     *
     * @return void
     */
    public function setTemplateFile($sTplFile)
    {
        $this->sTplFile = $sTplFile;
    }

    /**
     * @return bool
     */
    protected function isCodeUnset()
    {
        return empty($this->sCode);
    }
}
