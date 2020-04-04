<?php
/**
 * @title          PH7 Exception Class
 * @desc           Exception handling and displaying exception message.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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
