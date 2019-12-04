<?php
/**
 * @title            Update Picture Ajax Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Module / Webcam / Asset / Ajax
 * @version          1.6
 */

/*
 * This code was inspired by Martin Angelov's tutorial: http://tutorialzine.com/2011/04/jquery-webcam-photobooth/
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\UserException;
use PH7\Framework\File\Stream;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Util\Various;

class UploadPictureAjax
{
    const PENDING_FOLDER_NAME = 'pending';
    const APPROVED_FOLDER_NAME = 'img';
    const MD5_BLANK_IMAGE = '7d4df9cc423720b7f1f3d672b89362be';
    const IMAGE_EXTENSION = '.jpg';

    /** @var string */
    private $sPath;

    /** @var string */
    private $sTmpPathFile;

    /** @var string */
    private $sOriginalPathFile;

    /** @var string */
    private $sThumbPathFile;

    /** @var string */
    private $sFile;

    /**
     * @return self
     *
     * @throws UserException
     */
    public function checkRequestMethod()
    {
        /**
         * This file receives the JPEG snapshot from webcam.swf as a POST request.
         */

        // We only need to handle POST requests:
        if ((new Http)->getMethod() !== Http::METHOD_POST) {
            throw new UserException('The method must be post request!');
        }

        return $this;
    }

    public function generatePath()
    {
        $sUploadFolder = $this->getUploadFolder();

        $this->sFile = Various::genRnd() . self::IMAGE_EXTENSION;
        $this->sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'webcam/picture/';
        $this->sTmpPathFile = $this->sPath . 'tmp/' . $this->sFile;
        $this->sOriginalPathFile = $this->sPath . $sUploadFolder . '/original/' . $this->sFile;
        $this->sThumbPathFile = $this->sPath . $sUploadFolder . '/thumb/' . $this->sFile;

        return $this;
    }

    public function save()
    {
        // The JPEG snapshot is sent as raw input:
        $rInput = Stream::getInput();

        // Blank image. We don't need this one.
        if (md5($rInput) === self::MD5_BLANK_IMAGE) {
            exit(1);
        }

        $rResult = file_put_contents($this->sTmpPathFile, $rInput);
        if (!$rResult) {
            echo '{
        "error"     : 1,
        "message"   : "Failed save the image. Make sure you chmod the uploads folder and its subfolders to 777."
        }';
            exit;
        }

        return $this;
    }

    /**
     * @return self
     *
     * @throws UserException
     */
    public function checkImg()
    {
        if ($this->isInvalidImageType()) {
            unlink($this->sTmpPathFile);
            throw new UserException('Image type invalid!');
        }

        return $this;
    }

    public function renameImg()
    {
        // Moving the temporary file to the originals folder:
        rename($this->sTmpPathFile, $this->sOriginalPathFile);
        $this->sTmpPathFile = $this->sOriginalPathFile;

        return $this;
    }

    public function resizeImg()
    {
        // Using the GD library to resize
        // the image into a thumbnail:

        $sOrigImage = imagecreatefromjpeg($this->sTmpPathFile);
        $rNewImage = imagecreatetruecolor(154, 110);
        imagecopyresampled(
            $rNewImage,
            $sOrigImage,
            0,
            0,
            0,
            0,
            154,
            110,
            520,
            370
        );

        imagejpeg($rNewImage, $this->sThumbPathFile);

        return $this;
    }

    public function display()
    {
        $sFile = $this->isWebcamPictureManualApproval() ? '../../' . UserDesignCore::PENDING_IMG_FILENAME : $this->sFile;

        return '{"status":1,"message":"Success!","filename":"' . $sFile . '"}';
    }

    /**
     * @return string
     */
    private function getUploadFolder()
    {
        return $this->isWebcamPictureManualApproval() ? self::PENDING_FOLDER_NAME : self::APPROVED_FOLDER_NAME;
    }

    /**
     * @return bool
     */
    private function isWebcamPictureManualApproval()
    {
        return DbConfig::getSetting('webcamPictureManualApproval') == 1;
    }

    /**
     * @return bool
     */
    private function isInvalidImageType()
    {
        return getimagesize($this->sTmpPathFile)['mime'] !== 'image/jpeg';
    }
}

// Init Class!
echo (new UploadPictureAjax)
    ->checkRequestMethod()
    ->generatePath()
    ->save()
    ->checkImg()
    ->renameImg()
    ->resizeImg()
    ->display();
