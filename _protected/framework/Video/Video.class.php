<?php
/**
 * @title            Video Class
 * @desc             Class is used to create/manipulate videos using FFmpeg.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Video
 * @link             https://ph7builder.com
 */

namespace PH7\Framework\Video;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Date\Various;
use PH7\Framework\File\File;
use PH7\Framework\File\MissingProgramException;
use PH7\Framework\File\TooLargeException;
use PH7\Framework\File\Upload;

class Video extends Upload
{
    public const SUPPORTED_TYPES = [
        'mov' => 'video/mov',
        'avi' => 'video/avi',
        'flv' => 'video/flv',
        'mp4' => 'video/mp4',
        'mpg' => 'video/mpg',
        'mpeg' => 'video/mpeg',
        'wmv' => 'video/wmv',
        'ogg' => 'video/ogg',
        'ogv' => 'video/ogv',
        'webm' => 'video/webm',
        'mkv' => 'video/mkv'
    ];

    private const MP4_TYPE = 'mp4';

    private File $oFile;

    private string $sMimeType;

    private string $sExt;

    private string $sFfmpegPath;

    private array $aFile;

    /**
     * @param array $aFile Example: $_FILES['video']
     *
     * @throws MissingProgramException If FFmpeg is not installed.
     */
    public function __construct(array $aFile)
    {
        $this->sFfmpegPath = Config::getInstance()->values['video']['handle.ffmpeg_path'];

        if (!file_exists($this->sFfmpegPath)) {
            $sMsg = t('FFmpeg is not installed on the server or the path cannot be found. Please install and configure the path in "~/YOUR-PROTECTED-FOLDER/app/configs/config.ini" or contact the administrator of the site/server or web hosting by saying the problem.');
            throw new MissingProgramException($sMsg);
        }

        $this->oFile = new File;
        $this->aFile = $aFile;
        $this->sMimeType = (string)($this->aFile['type'] ?? '');
        $this->sExt = $this->oFile->getFileExt((string)($this->aFile['name'] ?? ''));

        /** Attributes from "Upload" abstract class **/
        $this->sMaxSize = Config::getInstance()->values['video']['upload.max_size'];
        $this->iFileSize = (int)$this->aFile['size'];
    }

    /**
     * @return bool
     *
     * @throws TooLargeException If the video file is not found.
     */
    public function validate()
    {
        if (!is_uploaded_file($this->aFile['tmp_name'])) {
            if (isDebug()) {
                throw new TooLargeException('Video file could not be uploaded. Possibly too large.');
            } else {
                return false;
            }
        }

        $sMimeType = $this->detectMimeType();
        $bValidByDetectedMime = in_array($sMimeType, self::SUPPORTED_TYPES, true);
        $bValidByClientMime = in_array($this->sMimeType, self::SUPPORTED_TYPES, true);
        $bValidByExtension = array_key_exists($this->sExt, self::SUPPORTED_TYPES);

        return $bValidByDetectedMime || ($bValidByClientMime && $bValidByExtension);
    }

    /**
     * @param string $sFile
     *
     * @return bool
     */
    public function save($sFile)
    {
        return move_uploaded_file($this->aFile['tmp_name'], $sFile);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->aFile['name'];
    }

    /**
     * Convert video file and the extension video type.
     *
     * @param string $sFile New renamed file name.
     *
     * @return string The new name that you entered in the parameter of this method.
     */
    public function rename($sFile)
    {
        $sParams = ''; // By default, we don't use parameter

        $sType = $this->oFile->getFileExt($sFile); // Get the new format
        if ($sType === self::MP4_TYPE) {
            $sParams = '-c copy -copyts';
        }

        $sInput = escapeshellarg($this->aFile['tmp_name']);
        $sOutput = escapeshellarg($sFile);
        $this->executeCommand('-i', "$sInput $sParams $sOutput");

        return $sFile;
    }

    /**
     * Generate a thumbnail with FFmpeg.
     *
     * @param string $sPicturePath
     * @param int $iSeconds
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return string The thumbnail file that you entered in the parameter of this method.
     */
    public function thumbnail($sPicturePath, $iSeconds, $iWidth, $iHeight)
    {
        $sInput = escapeshellarg($this->aFile['tmp_name']);
        $sOutput = escapeshellarg($sPicturePath);
        $this->executeCommand(
            '-itsoffset',
            "-$iSeconds -i $sInput -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$iWidth}x{$iHeight} $sOutput"
        );

        return $sPicturePath;
    }

    /**
     * Gets video duration.
     *
     * @return int Seconds.
     */
    public function getDuration()
    {
        $sInput = escapeshellarg($this->aFile['tmp_name']);
        $sTime = $this->executeCommand(
            '-i ',
            "$sInput 2>&1 | grep -i 'duration' | cut -d ' ' -f 4 | sed s/,//"
        );

        return Various::timeToSec($sTime);
    }

    /**
     * Get Type Video File.
     *
     * @return string The extension of the video without the dot.
     */
    public function getExt()
    {
        return $this->sExt;
    }

    /**
     * Execute a FFmpeg command.
     *
     * @param string $sFlag
     * @param string $sArgument
     *
     * @return void
     */
    private function executeCommand($sFlag, $sArgument)
    {
        exec(
            sprintf(
                '%s %s %s',
                escapeshellarg($this->sFfmpegPath),
                $sFlag,
                $sArgument
            )
        );
    }

    private function detectMimeType(): string
    {
        if (function_exists('finfo_open') && is_file($this->aFile['tmp_name'])) {
            $rFileInfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($rFileInfo !== false) {
                $sDetectedType = finfo_file($rFileInfo, $this->aFile['tmp_name']);
                finfo_close($rFileInfo);
                if (is_string($sDetectedType) && $sDetectedType !== '') {
                    return $sDetectedType;
                }
            }
        }

        return $this->sMimeType;
    }

    /**
     * Remove temporary file.
     */
    public function __destruct()
    {
        // If it exists, delete the temporary video file
        $this->oFile->deleteFile($this->aFile['tmp_name']);
    }
}
