<?php
/**
 * @title            Video Class
 * @desc             Class is used to create/manipulate videos using FFmpeg.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @link             https://ph7cms.com
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
    const SUPPORTED_TYPES = [
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

    const MP4_TYPE = 'mp4';

    /** @var File */
    private $oFile;

    /** @var string */
    private $sType;

    /** @var string */
    private $sFfmpegPath;

    /** @var array */
    private $aFile;

    /**
     * @param array $aFile Example: $_FILES['video']
     *
     * @throws MissingProgramException If FFmpeg is not installed.
     */
    public function __construct($aFile)
    {
        $this->sFfmpegPath = Config::getInstance()->values['video']['handle.ffmpeg_path'];

        if (!file_exists($this->sFfmpegPath)) {
            $sMsg = t('FFmpeg is not installed on the server or the path cannot be found. Please install and configure the path in "~/YOUR-PROTECTED-FOLDER/app/configs/config.ini" or contact the administrator of the site/server or web hosting by saying the problem.');
            throw new MissingProgramException($sMsg);
        }

        $this->oFile = new File;
        $this->aFile = $aFile;
        $this->sType = $this->aFile['type'];

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

        return in_array($this->sType, self::SUPPORTED_TYPES, true);
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

        $this->executeCommand('-i', "{$this->aFile['tmp_name']} $sParams $sFile");

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
        $this->executeCommand(
            '-itsoffset',
            "-$iSeconds -i {$this->aFile['tmp_name']} -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$iWidth}x{$iHeight} $sPicturePath"
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
        $sTime = $this->executeCommand(
            '-i ',
            "{$this->aFile['tmp_name']} 2>&1 | grep -i 'duration' | cut -d ' ' -f 4 | sed s/,//"
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
        return $this->sType;
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
                $this->sFfmpegPath,
                $sFlag,
                $sArgument
            )
        );
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
