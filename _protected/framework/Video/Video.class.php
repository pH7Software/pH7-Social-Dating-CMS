<?php
/**
 * @title            Video Class
 * @desc             Class is used to create/manipulate videos using FFmpeg.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Video
 * @version          0.3
 * @link             http://hizup.com
 */

namespace PH7\Framework\Video;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\File,
PH7\Framework\Date\Various,
PH7\Framework\Config\Config;

class Video
{

    private $sType, $sFfmpegPath, $aFile, $iWidth, $iHeight, $iMaxSize, $iQuality;

    /**
     * @constructor
     * @param array $aFile Example: $_FILES['video']
     * @return void
     * @throws \PH7\Framework\File\Exception If FFmpeg is not installed.
     * @throws \PH7\Framework\Error\CException\PH7BadMethodCallException If the video file is not found.
     */
    public function __construct($aFile)
    {
        $this->oFile = new File;
        $this->iWidth = 480;
        $this->iHeight = 295;
        $this->iQuality = 100;
        $this->iMaxSize = (int) Config::getInstance()->values['video']['upload.max_size'];
        $this->sFfmpegPath = Config::getInstance()->values['video']['handle.ffmpeg_path'];

        if(!file_exists($this->sFfmpegPath))
        {
            throw new \PH7\Framework\File\Exception('FFmpeg is not installed on your server, please install and configure the path in "~/YOUR-PROTECTED-FOLDER/app/configs/config.ini"');
        }

        if(!empty($aFile))
        {
            $this->aFile = $aFile;
            $this->sType = $this->oFile->getFileExt($this->aFile['name']);

            if(!is_file($this->aFile['tmp_name']))
            {
                throw new \PH7\Framework\Error\CException\PH7BadMethodCallException('Video file not found: The video file ' . $this->aFile['tmp_name'] . ' could not be found.');
            }

        }
    }

    /**
     * @desc Video Validate.
     * @return boolean
     */
    public function validate()
    {
        switch($this->sType)
        {
            // Files supported List.
            case 'mov':
            case 'avi':
            case 'flv':
            case 'mp4':
            case 'mpg':
            case 'mpeg':
            case 'wmv':
            case 'ogg':
            case 'ogv':
            case 'webm':
                return true;
            break;

            default:
                return false;
        }
    }

     /**
      * @desc Save Video.
      * @param string $sFile
      * @return boolean
      */
    public function save($sFile)
    {
        return (move_uploaded_file($this->aFile['tmp_name'], $sFile));
    }

    /**
     * @desc Get File Name.
     * @return string
     */
    public function getFileName()
    {
        return $this->aFile['name'];
    }

    /**
     * @desc Check Video Size .
     * @return boolean
     */
    public function check()
    {
        $iUploadMaxSize = ($this->iMaxSize*1024*1024);

        if($this->aFile['size'] <= $iUploadMaxSize)
        {
            return true;
        }

        return false;
    }

    /**
     * @desc Convert video file and the extension video type.
     * @param string $sFile.
     * @return string The new name that you entered in the parameter of this method.
     */
    public function rename($sFile)
    {
        $sParams = ''; // By default, we don't use parameter

        $sType = $this->oFile->getFileExt($sFile); // Get the new format
        if($sType == 'mp4')
            $sParams = '-c copy -copyts';

        exec("$this->sFfmpegPath -i {$this->aFile['tmp_name']} $sParams $sFile");
        return $sFile;
    }

    /*
     * @desc Generate a thumbnail with FFmpeg.
     * @param string $sPicturePath
     * @param integer $iWidth
     * @param integer $iHeight
     * @return string The thumbnail file that you entered in the parameter of this method.
     */
    public function thumbnail($sPicturePath, $iSeconds, $iWidth, $iHeight)
    {
        exec($this->sFfmpegPath . ' -itsoffset -' . $iSeconds . ' -i ' . $this->aFile['tmp_name'] . '  -vcodec mjpeg -vframes 1 -an -f rawvideo -s ' . $iWidth . 'x' . $iHeight . ' ' . $sPicturePath);
        return $sPicturePath;
    }

    /**
     * @desc Gets video duration.
     * @return integer Seconds.
     */
    public function getDuration()
    {
         $sTime = exec($this->sFfmpegPath . ' -i ' . $this->aFile['tmp_name'] . ' 2>&1 | grep "Duration" | cut -d \' \' -f 4 | sed s/,//');
         return Various::timeToSec($sTime);
     }

    /**
     * @desc Get Type Video File.
     * @return string The extension of the video without the dot.
     */
    public function getExt()
    {
        return $this->sType;
    }

    /**
     * @desc Destruction of attributes and temporary file.
     */
    public function __destruct()
    {
        // If it exists, delete the temporary video
        $this->oFile->deleteFile($this->aFile['tmp_name']);

        unset(
            $this->oFile,
            $this->sType,
            $this->sFfmpegPath,
            $this->aFile,
            $this->iWidth,
            $this->iHeight,
            $this->iMaxSize,
            $this->iQuality
        );
    }

}
