<?php
/***************************************************************************
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Error\CException\PH7RuntimeException;

class EmptyCodeException extends PH7RuntimeException
{
    const CURLY_SYNTAX = 1;
    const TAL_SYNTAX = 2;
}
