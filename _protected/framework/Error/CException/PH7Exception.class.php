<?php
/**
 * @title          PH7 Exception Class
 * @desc           Exception handling and displaying exception message.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error / CException
 * @version        1.0
 */

namespace PH7\Framework\Error\CException;

defined('PH7') or exit('Restricted access');

use Exception;
use PH7\Framework\Error\Debug;
use PH7\Framework\Error\LoggerExcept;
use PH7\Framework\Page\Page;

class PH7Exception extends Exception
{
    use Escape;

    /**
     * @param string $sMsg
     */
    public function __construct($sMsg)
    {
        parent::__construct($sMsg);
        $this->init($sMsg);
    }

    /**
     * Sends the exception data to the logger class.
     *
     * @param Exception $oExcept
     */
    public static function launch(Exception $oExcept)
    {
        if (Debug::is()) {
            Page::exception($oExcept);
        } else {
            (new LoggerExcept)->except($oExcept); // Set Exception in Error Log
            Page::error500();
        }
    }
}

