<?php

/**
 * @title          Error Exception Class
 *
 * @desc           Management error messages for the Exceptions.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @version        1.1
 */

namespace PH7\Framework\Error\CException {
    defined('PH7') or exit('Restricted access');

    /**
     * This function for display errors with the ErrorException class which is defined by PH7Exception class.
     */
    final class ErrException extends \ErrorException
    {
        public function __toString()
        {
            switch ($this->severity) {
                case E_USER_ERROR: // If the user issues a fatal error
                    $sType = 'Fatal error ';
                    break;
                case E_WARNING: // If PHP issues a warning
                case E_USER_WARNING: // If the user issues a warning
                    $sType = 'Warning error';
                    break;
                case E_NOTICE: // If PHP issues a notice
                case E_USER_NOTICE: // If the user issues a notice
                    $sType = 'Notice error';
                    break;
                default: // Unknown error
                    $sType = 'Unknown error';
                    break;
            }

            return '<strong>' . $sType . '</strong> : [' . $this->code . '] ' . htmlspecialchars($this->message, ENT_QUOTES) . '<br /><strong>' . $this->file . '</strong> to line <strong>' . $this->line . '</strong>';
        }
    }
}

namespace {
    use PH7\Framework\Error\CException\ErrException;
    use PH7\Framework\Error\CException\PH7Exception;

    /**
     * The code serves as severity
     * Refer to the predefined constants for more information: http://php.net/manual/errorfunc.constants.php.
     *
     * @param int    $iCode
     * @param string $sMessage
     * @param string $sFile
     * @param string $sLine
     *
     * @throws ErrException
     */
    function errExcept($iCode, $sMessage, $sFile, $sLine)
    {
        throw new ErrException($sMessage, 0, $iCode, $sFile, $sLine);
    }

    function customExcept(\Throwable $oExcept)
    {
        PH7Exception::launch($oExcept);
    }

    set_error_handler('errExcept');
    set_exception_handler('customExcept');
}
