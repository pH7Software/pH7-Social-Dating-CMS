<?php
/**
 * @title          Logger Except Class
 * @desc           Handler Logger Exception Management.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error
 * @version        1.3
 */

namespace PH7\Framework\Error;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\File,
PH7\Framework\Ip\Ip,
PH7\Framework\Http\Http,
PH7\Framework\Mvc\Model\Engine\Db;

final class LoggerExcept extends Logger
{

    public function __construct()
    {
        try
        {
            \PH7\Framework\Mvc\Router\FrontController::getInstance()->_databaseInitialize();
        }
        catch (\PH7\Framework\Mvc\Model\Engine\Exception $oE)
        {
            // If we are not in development mode, we display an error message to avoid showing information on the database.
            if (!Debug::is()) exit('Could not connect to database server!');
        }

        parent::__construct();
    }

    /**
     * Write to the logfile.
     *
     * @param object $oExcept \Exception object.
     * @return void
     */
    public function except(\Exception $oExcept)
    {
        // Time: Set the log date/time.
        // IP: The IP address of the client.
        // UserAgent: The User Agent of the Browser Web.
        // UrlPag: The URL page where the exception is thrown.
        // Query: The request for such a page.
        // Message: constains the error message.
        // Level: contains the log level.
        // File: constains the file name.
        // Line: constains the line number.
        $sAgent = (null !== ($mAgent = $this->browser->getUserAgent() )) ? $mAgent : 'NO USER AGENT';
        $sQuery = (null !== ($mQuery = (new Http)->getQueryString() )) ? $mQuery : 'NO QUERY STRING';
        $aLog = [
            'Time'        => $this->dateTime->get()->dateTime(),
            'IP'          => Ip::get(),
            'UserAgent'   => $sAgent,
            'UrlPag'      => $this->httpRequest->currentUrl(),
            'Query'       => $sQuery,
            'Message'     => $oExcept->getMessage(),
            'Level'       => $oExcept->getCode(),
            'File'        => $oExcept->getFile(),
            'Line'        => $oExcept->getLine()
        ];

        // Encode the line
        $sContents = json_encode($aLog) . File::EOL . File::EOL . File::EOL;
        switch ($this->config->values['logging']['log_handler'])
        {
            case 'file':
            {
                $sFullFile = $this->sDir . static::EXCEPT_DIR . $this->sFileName . '.json';
                $sFullGzipFile = $this->sDir . static::EXCEPT_DIR . static::GZIP_DIR . $this->sFileName . '.gz';

                // If the log file is larger than 5 Mo then it compresses it into gzip
                if (file_exists($sFullFile) && filesize($sFullFile) >= 5 * 1024 * 1024)
                {
                    $rHandler = @gzopen($sFullGzipFile, 'a') or exit('Unable to write to log file gzip.');
                    gzwrite($rHandler, $sContents);
                    gzclose($rHandler);
                }
                else
                {
                    $rHandler = @fopen($sFullFile, 'a') or exit('Unable to write to log file.');
                    fwrite($rHandler, $sContents);
                    fclose($rHandler);
                }
            }
            break;

            case 'database':
            {
                $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('LogError') . 'SET logError = :line');
                $rStmt->execute(array(':line' => $sContents));
                Db::free($rStmt);
            }
            break;

            case 'email':
            {
                $aInfo = [
                    'to' => $this->config->values['logging']['bug_report_email'],
                    'subject' => t('Errors Reporting of the pH7 Framework')
                ];

                (new \PH7\Framework\Mail\Mail)->send($aInfo, $sContents, false);
            }
            break;

            default:
                exit('Invalid Log Option.');
        }
    }

}
