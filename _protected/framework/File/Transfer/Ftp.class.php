<?php
/**
 * @title            FTP Class
 * @desc             Management of the file transfer protocol.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Transfer
 * @version          0.8
 * @link             http://ph7cms.com
 * @linkDoc          http://php.net/manual/book.ftp.php
 */

namespace PH7\Framework\File\Transfer;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\File\Permission\PermissionException;
use RuntimeException;

class Ftp extends File
{
    /*** Alias ***/
    const ASCII = FTP_ASCII;
    const TEXT = FTP_TEXT;
    const BINARY = FTP_BINARY;
    const IMAGE = FTP_IMAGE;
    const TIMEOUT_SEC = FTP_TIMEOUT_SEC;
    const AUTOSEEK = FTP_AUTOSEEK;
    const AUTORESUME = FTP_AUTORESUME;
    const FAILED = FTP_FAILED;
    const FINISHED = FTP_FINISHED;
    const MOREDATA = FTP_MOREDATA;

    /** @var string */
    private $sHost;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sPassword;

    /** @var string */
    private $sPath;

    /** @var resource */
    private $rStream;

    /**
     * @param string $sHost
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sPath
     *
     * @throws MissingExtensionException If FTP PHP extension is not installed.
     */
    public function __construct($sHost, $sUsername, $sPassword, $sPath = '/')
    {
        if (!extension_loaded('ftp')) {
            throw new MissingExtensionException('FTP PHP extension is not installed!');
        }

        // Attribute assignments
        $this->sHost = $sHost;
        $this->sUsername = $sUsername;
        $this->sPassword = $sPassword;
        $this->sPath = $this->checkExtDir($sPath);
    }

    /**
     * Connect to FTP server.
     *
     * @param bool $bSsl For a SSL-FTP connection.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     *
     * @throws RuntimeException If the host is incorrect.
     */
    public function connect($bSsl = false)
    {
        $sConnFuncName = $bSsl ? 'ftp_ssl_connect' : 'ftp_connect';

        if (!$this->rStream = $sConnFuncName($this->sHost)) {
            throw new RuntimeException('Cannot connect to ' . $this->sHost);
        }

        return ftp_login($this->rStream, $this->sUsername, $this->sPassword);
    }

    /**
     * Check if it is a file.
     *
     * @param string $sFile
     *
     * @return bool
     */
    public function existFile($sFile)
    {
        return is_array(ftp_nlist($this->rStream, $sFile));
    }

    /**
     * Check if it is a directory.
     *
     * @param $sDir string
     *
     * @return bool
     */
    public function existDir($sDir)
    {
        $sCurrentDir = $this->getCurrentDir();

        if ($this->changeDir($sCurrentDir)) {
            $this->changeDir($sDir);
            $sNewDir = $this->getCurrentDir();

            return empty($sNewDir);
        }

        return false;
    }

    /**
     * Creates a directory if they are in an array. If it does not exist and
     * allows the creation of nested directories specified in the pathname.
     *
     * @param string|array $mDir
     * @param int (octal) $iMode
     *
     * @return void
     *
     * @throws PermissionException If the file cannot be created.
     */
    public function createDir($mDir, $iMode = Chmod::MODE_EXEC_READ)
    {
        if (is_array($mDir)) {
            foreach ($mDir as $sD) {
                $this->createDir($sD);
            }
        } else {
            if (!$this->existDir($mDir)) {
                if (@ftp_mkdir($this->rStream, $mDir)) {
                    $this->chmod($mDir, $iMode); // For Unix OS
                } else {
                    throw new PermissionException(
                        sprintf('Cannot create "%s" directory.<br /> Please verify that the directory permission is in writing mode.', $mDir)
                    );
                }
            }
        }
    }

    /**
     * Downloads a file from the FTP server.
     *
     * @param string $sFrom Full path to the file on the server.
     * @param string $sTo Full path where the file will be placed on the computer.
     *
     * @return void
     *
     * @throws UploadingFileException If the file cannot be transferred to the computer.
     */
    public function get($sFrom, $sTo)
    {
        $iType = $this->getFileMode($sTo);

        if (!@ftp_get($this->rStream, $sFrom, $sTo, $iType)) {
            throw new UploadingFileException('There was a problem while uploading from: ' . $sFrom);
        }
    }

    /**
     * Uploads a file to the FTP server.
     *
     * @param string $sFrom Full path to the file on the computer.
     * @param string $sTo Full path where the file will be placed on the server.
     * @param int (octal) $iMode
     *
     * @return void
     *
     * @throws UploadingFileException If the file cannot be transferred to the server.
     */
    public function put($sFrom, $sTo, $iMode = Chmod::MODE_WRITE_READ)
    {
        $iType = $this->getFileMode($sTo);

        if (@ftp_put($this->rStream, $sTo, $sFrom, $iType)) {
            $this->chmod($sTo, $iMode); // For Unix OS
        } else {
            throw new UploadingFileException('There was a problem while uploading from: ' . $sFrom);
        }
    }

    /**
     * Rename or move a file from one location to another on the FTP server.
     *
     * @param string $sFrom Full path to the file.
     * @param string $sTo Full path to the file.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rename($sFrom, $sTo)
    {
        if (!$this->existFile($sFrom)) {
            return false;
        }

        return ftp_rename($this->rStream, $sFrom, $sTo);
    }

    /**
     * Deletes a file or files if they are in an array.
     * If the file does not exist, the function does nothing.
     *
     * @param string|array $mFile
     *
     * @return bool
     */
    public function deleteFile($mFile)
    {
        if (is_array($mFile)) {
            $bRet = false;
            foreach ($mFile as $sFile) {
                if (!$bRet = $this->deleteFile($sFile)) {
                    return false;
                }
            }
            return $bRet;
        } else {
            if ($this->existFile($mFile)) {
                return ftp_delete($this->rStream, $mFile);
            }
        }
    }

    /**
     * Delete directories recursively.
     *
     * @param string $sPath The path
     *
     * @return bool
     */
    public function deleteDir($sPath)
    {
        return $this->existFile($sPath) ? $this->deleteFile($sPath) : array_map([$this, 'deleteDir'], glob($sPath . '/*')) === @ftp_rmdir($this->rStream, $sPath);
    }

    /**
     *  Allocates space for a file to be uploaded.
     *
     * @param string $sFile
     *
     * @return bool|string Returns TRUE on success or a message from the server in case of failure.
     */
    public function alloc($sFile)
    {
        return !ftp_alloc($this->rStream, $this->size($sFile), $sRes) ? $sRes : true;
    }

    /**
     * Retrieve the size of a file from the FTP server.
     *
     * @param string $sFile
     *
     * @return int Returns the file size on success, or -1 on error.
     */
    public function getSize($sFile)
    {
        return ftp_size($this->rStream, $sFile);
    }

    /**
     * Get the current directory name.
     *
     * @return string Current directory name.
     */
    public function getCurrentDir()
    {
        return ftp_pwd($this->rStream);
    }

    /**
     * Changes the current directory.
     *
     * @param string $sDir
     *
     * @return bool Returns TRUE on success or FALSE on failure. If changing directory fails, PHP will also throw a warning.
     */
    public function changeDir($sDir)
    {
        return ftp_chdir($this->rStream, $sDir);
    }

    /**
     * Changes permission on a file or directory.
     *
     * @param string $sFile
     * @param int $iMode Octal Permission for the file.
     *
     * @return bool
     */
    public function chmod($sFile, $iMode)
    {
        return @ftp_chmod($this->rStream, $iMode, $sFile);
    }

    /**
     * Closes an FTP connection.
     *
     * @return void
     */
    public function close()
    {
        if (!empty($this->rStream) && $this->rStream !== false) {
            ftp_close($this->rStream);
        }
    }

    /**
     * Requests execution of a command on the FTP server.
     *
     * @param string $sCommand
     *
     * @return bool Returns TRUE if the command was successful (server sent response code: 200); otherwise returns FALSE.
     */
    public function exec($sCommand)
    {
        return ftp_exec($this->rStream, $sCommand);
    }

    /**
     * Get mode of the file.
     *
     * @param string $sFile
     *
     * @return int
     */
    protected function getFileMode($sFile)
    {
        return $this->isBinary($sFile) ? static::BINARY : static::ASCII;
    }

    /**
     * Closes the FTP connection.
     */
    public function __destruct()
    {
        $this->close();
    }
}
