<?php
/**
 * @title          PH7 User Exception Class
 * @desc           User Exception handling.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error / CException
 * @version        1.0
 */

namespace PH7\Framework\Error\CException;
defined('PH7') or exit('Restricted access');

class UserException extends \Exception
{

    use Escape;

    public function __construct($sMsg)
    {
        static::init($sMsg);
    }

}
