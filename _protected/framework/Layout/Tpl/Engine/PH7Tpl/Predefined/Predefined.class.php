<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Main Predefined Abstract Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

defined('PH7') or exit('Restricted access');

abstract class Predefined
{
    const PHP_OPEN = '<?php ';
    const PHP_CLOSE = '?>';
    const WRITE = 'echo ';

    /** @var string */
    private $sCode;

    /** @var string */
    private $sLeftDelimiter = '{';

    /** @var string */
    private $sRightDelimiter = '}';

    /**
     * @param string $sCode
     */
    public function __construct($sCode)
    {
        $this->sCode = $sCode;
    }

    /**
     * Assign the global variables/functions.
     *
     * @return self
     */
    abstract public function assign();

    /**
     * Gets the parsed variables.
     *
     * @return string
     */
    public function get()
    {
        return $this->sCode;
    }

    /**
     * @param string $sDelimiter
     */
    public function setLeftDelimiter($sDelimiter)
    {
        $this->sLeftDelimiter = $sDelimiter;
    }

    /**
     * @param string $sDelimiter
     */
    public function setRightDelimiter($sDelimiter)
    {
        $this->sRightDelimiter = $sDelimiter;
    }

    /**
     * Adding Variable.
     *
     * @param string $sKey
     * @param string $sValue
     * @param bool Print the variable. Default TRUE
     *
     * @return void
     */
    protected function addVar($sKey, $sValue, $bPrint = true)
    {
        $this->sCode = str_replace('$' . $sKey, $sValue, $this->sCode);
        $this->sCode = str_replace(
            $this->sLeftDelimiter . $sKey . $this->sRightDelimiter,
            static::PHP_OPEN . ($bPrint ? static::WRITE : '') . $sValue . static::PHP_CLOSE,
            $this->sCode
        );
    }

    /**
     * Adding Function.
     *
     * @param string $sKey
     * @param string $sValue
     *
     * @return void
     */
    protected function addFunc($sKey, $sValue)
    {
        $this->sCode = preg_replace(
            '#' . $sKey . '#',
            static::PHP_OPEN . static::WRITE . $sValue . static::PHP_CLOSE,
            $this->sCode
        );
    }
}
