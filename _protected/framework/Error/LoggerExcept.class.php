<?php
/**
 * @title          Logger Except Class
 * @desc           Handler Logger Exception Management.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error
 * @version        2.0
 */

namespace PH7\Framework\Error;

defined('PH7') or exit('Restricted access');

use Exception;
use PH7\DbTableName;
use PH7\Framework\File\File;
use PH7\Framework\Http\Http;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Exception as ModelException;
use PH7\Framework\Mvc\Router\FrontController;

final class LoggerExcept extends Logger
{
    const MAX_UNCOMPRESSED_SIZE = 5; // Size in megabytes

    /*** Log handler types ***/
    const FILE_LOG_HANDLER_TYPE = 'file';
    const DATABASE_LOG_HANDLER_TYPE = 'database';
    const EMAIL_LOG_HANDLER_TYPE = 'email';

    public function __construct()
    {
        try {
            FrontController::getInstance()->_initializeDatabase();
        } catch (ModelException $oE) {
            // If we are not in development mode, we display an error message to avoid showing information on the database.
            if (!Debug::is()) {
                exit('Could not connect to database server!');
            }
        }

        parent::__construct();
    }

    /**
     * Write to the logfile.
     *
     * @param Exception $oExcept
     *
     * @return void
     */
    public function except(Exception $oExcept)
    {
        // Time: Set the log date/time.
        // IP: The IP address of the client.
        // UserAgent: The User Agent of the Browser Web.
        // UrlPag: The URL page where the exception is thrown.
        // Query: The request for such a page.
        // Message: contains the error message.
        // Level: contains the log level.
        // File: contains the file name.
        // Line: contains the line number.
        $sAgent = (null !== ($mAgent = $this->browser->getUserAgent())) ? $mAgent : 'NO USER AGENT';
        $sQuery = (null !== ($mQuery = (new Http)->getQueryString())) ? $mQuery : 'NO QUERY STRING';
        $aLog = [
            'Time' => $this->dateTime->get()->dateTime(),
            'IP' => Ip::get(),
            'UserAgent' => $sAgent,
            'UrlPag' => $this->httpRequest->currentUrl(),
            'Query' => $sQuery,
            'Message' => $oExcept->getMessage(),
            'Level' => $oExcept->getCode(),
            'File' => $oExcept->getFile(),
            'Line' => $oExcept->getLine()
        ];

        // Encode the line
        $sContents = json_encode($aLog) . File::EOL . File::EOL . File::EOL;
        switch ($this->config->values['logging']['log_handler']) {
            case self::FILE_LOG_HANDLER_TYPE:
                $this->fileHandler($sContents);
                break;

            case self::DATABASE_LOG_HANDLER_TYPE:
                $this->sqlHandler($sContents);
                break;

            case self::EMAIL_LOG_HANDLER_TYPE:
                $this->emailHandler($sContents);
                break;

            default:
                exit(t('Invalid Log Option.'));
        }
    }

    /**
     * @param string $sContents
     *
     * @return void
     */
    private function fileHandler($sContents)
    {
        $sFullFile = $this->sDir . static::EXCEPT_DIR . $this->sFileName . '.json';
        $sFullGzipFile = $this->sDir . static::EXCEPT_DIR . static::GZIP_DIR . $this->sFileName . '.gz';

        if ($this->isGzipEligible($sFullFile)) {
            $sErrMsg = Debug::is() ? 'Unable to write: ' . $sFullGzipFile : 'Unable to write to log gzip file';
            $rHandler = @gzopen($sFullGzipFile, 'a') or exit($sErrMsg);
            gzwrite($rHandler, $sContents);
            gzclose($rHandler);
        } else {
            $sErrMsg = Debug::is() ? 'Unable to write: ' . $sFullFile : 'Unable to write to log file';
            $rHandler = @fopen($sFullFile, 'a') or exit($sErrMsg);
            fwrite($rHandler, $sContents);
            fclose($rHandler);
        }
    }

    /**
     * @param string $sContents
     *
     * @return void
     */
    private function sqlHandler($sContents)
    {
        $sSql = 'INSERT INTO' . Db::prefix(DbTableName::LOG_ERROR) . 'SET logError = :line';
        $rStmt = Db::getInstance()->prepare($sSql);
        $rStmt->execute([':line' => $sContents]);
        Db::free($rStmt);
    }

    /**
     * @param string $sContents
     *
     * @return void
     */
    private function emailHandler($sContents)
    {
        $aInfo = [
            'to' => $this->config->values['logging']['bug_report_email'],
            'subject' => t('Errors Reporting of pH7Framework')
        ];

        (new Mail)->send(
            $aInfo,
            $sContents,
            Mailable::TEXT_FORMAT
        );
    }

    /**
     * If the log file already exists and is larger than 5 Mb, then returns TRUE, FALSE otherwise.
     *
     * @param string $sFullFile Log file path.
     *
     * @return bool
     */
    private function isGzipEligible($sFullFile)
    {
        return is_file($sFullFile) && filesize($sFullFile) >= static::MAX_UNCOMPRESSED_SIZE * 1024 * 1024;
    }
}
