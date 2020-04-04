<?php
/**
 * @title            Exception Http Request
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Request
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Mvc\Request;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\UserException;
use PH7\Framework\Layout\Form\Message;

class WrongRequestMethodException extends UserException
{
    const GET_METHOD = 1;
    const POST_METHOD = 2;

    /**
     * @param string $sMethodName
     * @param int $iCode
     */
    public function __construct($sMethodName, $iCode)
    {
        parent::__construct(Message::wrongRequestMethodMsg($sMethodName), $iCode);
    }
}
