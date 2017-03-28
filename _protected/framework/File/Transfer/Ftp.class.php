<?php
/**
 * @title            FTP Class
 * @desc             Management of the file transfer protocol.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Transfer
 * @version          0.8
 * @link             http://hizup.com
 * @linkDoc          http://php.net/manual/book.ftp.php
 */

namespace PH7\Framework\File\Transfer;

defined('PH7') or exit('Restricted access');

use RuntimeException;

class Ftp extends \PH7\Framework\File\File
{
    /*** Alias ***/
    const
    ASCII = FTP_ASCII,
    TEXT = FTP_TEXT,
    BINARY = FTP_BINARY,
    IMAGE = FTP_IMAGE,
    TIMEOUT_SEC = FTP_TIMEOUT_SEC,
    AUTOSEEK = FTP_AUTOSEEK,
    AUTORESUME = FTP_AUTORESUME,
    FAILED = FTP_FAILED,
    FINISHED = FTP_FINISHED,
    MOREDATA = FTP_MOREDATA;

    private
    $_sHost,
    $_sUsername,
    $_sPassword,
    $_sPath,
    $_rStream;

    /**
     * Constructor.
     *
     * @param string $sHost
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sPath Default: '/'
     * @throws MissingExtensionException If FTP PHP extension is not installed.
     */
    public function __construct($sHost, $sUsername, $sPassword, $sPath = '/')
    {
        if (!extension_loaded('ftp')) {
            throw new MissingExtensionException('FTP PHP extension is not installed!');
        }

        // Attribute assignments
        $this->_sHost = $sHost;
        $this->_sUsername = $sUsername;
        $this->_sPassword = $sPassword;
        $this->_sPath = $this->checkExtDir($sPath);
    }

    /**
     * Connect to FTP server.
     *
     * @param boolean $bSsl For a SSL-FTP connection. Default: FALSE
     * @return boolean Returns TRUE on success or FALSE on failure.
     * @throws RuntimeException If the host is incorrect.
     */
    public function connect($bSsl = false)
    {
        $sConnFunc = ($bSsl) ? 'ftp_ssl_connect' : 'ftp_connect';

        if (!$this->_rStream = $sConnFunc($this->_sHost)) {
            throw new RuntimeException('Couldn\'t connect to \'' . $this->_sHost);
        }

        return ftp_login($this->_rStream, $this->_sUsername, $this->_sPassword);
    }

    /**
     * Check if it is a file.
     *
     * @param string $sFile
     * @return boolean
     */
    public function existFile($sFile)
    {
        return is_array(ftp_nlist($this->_rStream, $sFile));
    }

    /**
     * Check if it is a directory.
     *
     * @param $sDir string
     * @return boolean
     */
    public function existDir($sDir)
    {
        $sCurrentDir= $this->getCurrentDir();

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
     * @param mixed (string | array) $mDir
     * @param integer (octal) $iMode Default: 0755
     * @return void
     * @throws PermissionException If the file cannot be created.
     */
    public function createDir($mDir, $iMode = 0755)
    {
        if (is_array($mDir)) {
            foreach ($mDir as $sD) $this->createDir($sD);
        } else {
            if (!$this->existDir($mDir)) {
                if (@ftp_mkdir($this->_rStream, $mDir)) {
                    $this->chmod($mDir, $iMode); // For Unix OS
                } else {
                    throw new PermissionException('Error to create file: \'' . $mDir . '\'<br /> Please verify that the directory permission is in writing mode.');
                }
            }
        }
    }

    /**
     * Downloads a file from the FTP server.
     *
     * @param string $sFrom Full path to the file on the server.
     * @param string $sTo Full path where the file will be placed on the computer.
     * @return void
     * @throws UploadingFileException If the file cannot be transferred to the computer.
     */
    public function get($sFrom, $sTo)
    {
        $iType = $this->getFileMode($sTo);

        if (!@ftp_get($this->_rStream, $sFrom, $sTo, $iType))
            throw new UploadingFileException('There was a problem while uploading \'' . $sFrom);
    }

    /**
     * Uploads a file to the FTP server.
     *
     * @param string $sFrom Full path to the file on the computer.
     * @param string $sTo Full path where the file will be placed on the server.
     * @param integer (octal) $iMode Default: 0644
     * @return void
     * @throws UploadingFileException If the file cannot be transferred to the server.
     */
    public function put($sFrom, $sTo, $iMode = 0644)
    {
        $iType = $this->getFileMode($sTo);

        if (@ftp_put($this->_rStream, $sTo, $sFrom, $iType)) {
            $this->chmod($sTo, $iMode); // For Unix OS
        } else {
            throw new UploadingFileException('There was a problem while uploading \'' . $sFrom);
        }
    }

    /**
     * Rename or move a file from one location to another on the FTP server.
     *
     * @param string $sFrom Full path to the file.
     * @param string $sTo Full path to the file.
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function rename($sFrom, $sTo)
    {
        if (!$this->existFile($sFrom)) {
            return false;
        }

        return ftp_rename($this->_rStream, $sFrom, $sTo);
    }

    /**
     * Deletes a file or files if they are in an array.
     * If the file does not exist, the function does nothing.
     *
     * @param mixed (string | array) $mFile
     * @return boolean
     */
    public function deleteFile($mFile)
    {
        if (is_array($mFile)) {
            foreach ($mFile as $sF) {
                $this->deleteFile($sF);
            }
        } else {
            if ($this->existFile($mFile)) {
                ftp_delete($this->_rStream, $mFile);
            }
        }
    }

    /**
     * Delete directories recursively.
     *
     * @param string $sPath The path
     * @return boolean
     */
    public function deleteDir($sPath)
    {
        return $this->existFile($sPath) ? $this->deleteFile($sPath) : array_map(array($this, 'deleteDir'), glob($sPath . '/*')) === @ftp_rmdir($this->_rStream, $sPath);
    }

    /**
     *  Allocates space for a file to be uploaded.
     *
     * @param string $sFile
     * @return mixed (boolean | string) Returns TRUE on success or a message from the server in case of failure.
     */
    public function alloc($sFile)
    {
        return !ftp_alloc($this->_rStream, $this->size($sFile), $sRes) ? $sRes : true;
    }

    /**
     * Retrieve the size of a file from the FTP server.
     *
     * @param string $sFile
     * @return integer Returns the file size on success, or -1 on error.
     */
    public function getSize($sFile)
    {
        return ftp_size($this->_rStream, $sFile);
    }

    /**
     * Get the current directory name.
     *
     * @return string Current directory name.
     */
     public function getCurrentDir()
     {
         return ftp_pwd($this->_rStream);
     }

    /**
     * Changes the current directory.
     *
     * @param string $sDir
     * @return boolean Returns TRUE on success or FALSE on failure. If changing directory fails, PHP will also throw a warning.
     */
    public function changeDir($sDir)
    {
        return ftp_chdir($this->_rStream, $sDir);
    }

    /**
     * Changes permission on a file or directory.
     *
     * @param string $sFile
     * @param integer $iMode Octal Permission for the file.
     * @return boolean
     */
    public function chmod($sFile, $iMode)
    {
        return @ftp_chmod($this->_rStream, $iMode, $sFile);
    }

    /**
     * Closes an FTP connection.
     *
     * @return void
     */
    public function close()
    {
        if (!empty($this->_rStream) && $this->_rStream !== false) {
            ftp_close($this->_rStream);
        }
    }

    /**
     * Requests execution of a command on the FTP server.
     *
     * @param string $sCommand
     * @return Returns TRUE if the command was successful (server sent response code: 200); otherwise returns FALSE.
     */
    public function exec($sCommand)
    {
        return ftp_exec($this->_rStream, $sCommand);
    }

    /**
     * Get mode of the file.
     *
     * @param string $sFile
     * @return integer
     */
    protected function getFileMode($sFile)
    {
        return ($this->isBinary($sFile)) ? static::BINARY : static::ASCII;
    }

    /**
     * Destruction of the attributes and closes the FTP connection.
     */
    public function __destruct()
    {
        $this->close();

        unset(
            $this->_sHost,
            $this->_sUsername,
            $this->_sPassword,
            $this->_sPath,
            $this->_rStream
        );
    }
}
