<?php
/**
 * @title            Exception Http Request
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Request
 * @link             http://ph7cms.com
 */

namespace PH7\Framework\Mvc\Request;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\UserException;
use PH7\Framework\Layout\Form\Form;

class Exception extends UserException
{
    public function __construct($sMethodName)
    {
        parent::__construct(Form::wrongRequestMethodMsg($sMethodName));
    }
}
