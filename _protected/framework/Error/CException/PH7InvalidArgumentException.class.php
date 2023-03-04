<?php
/**
 * @title          PH7 Invalid Argument Exception Class
 * @desc           Exception Invalid Argument handling.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7/ Framework / Error / CException
 */

namespace PH7\Framework\Error\CException;

defined('PH7') or exit('Restricted access');

use InvalidArgumentException;

class PH7InvalidArgumentException extends InvalidArgumentException
{
    use Escape {
        strip as private;
    }

    /**
     * @param string $sMsg
     * @param int $iCode
     */
    public function __construct($sMsg, $iCode = 0)
    {
        parent::__construct($sMsg, $iCode);
        $this->strip($sMsg);
    }
}
