<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Syntax
 */

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax;

use PH7\Framework\Error\CException\PH7RuntimeException;

class EmptyCodeException extends PH7RuntimeException
{
    const CURLY_SYNTAX = 1;
    const TAL_SYNTAX = 2;
}
