<?php
/**
 * @title            Exception Http Request
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Request
 * @link             http://ph7builder.com
 */

namespace PH7\Framework\Mvc\Request;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\UserException;
use PH7\Framework\Layout\Form\Message;

class WrongRequestMethodException extends UserException
{
    // Import Message trait
    use Message;

    const GET_METHOD = 1;
    const POST_METHOD = 2;

    /**
     * @param string $sMethodName
     * @param int $iCode
     */
    public function __construct($sMethodName, $iCode)
    {
        $sMessage = self::wrongRequestMethodMsg($sMethodName);
        parent::__construct($sMessage, $iCode);
    }
}
