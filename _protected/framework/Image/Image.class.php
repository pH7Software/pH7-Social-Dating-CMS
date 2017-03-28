<?php
/**
 * @title            Image Class
 * @desc             Class is used to create/manipulate images using GD library.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Image
 * @version          1.1
 * @link             http://hizup.com
 * @linkGD           http://php.net/manual/book.image.php
 */

namespace PH7\Framework\Image;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\TooLargeException;

class Image
{
    /*** Alias ***/
    const JPG = IMAGETYPE_JPEG;
    const PNG = IMAGETYPE_PNG;
    const GIF = IMAGETYPE_GIF;
    const WEBP = 'image/webp';

    /** @var string */
    private $sFile;

    /** @var string */
    private $sType;

    /** @var resource */
    private $rImage;

    /** @var int */
    private $iWidth;

    /** @var int */
    private $iHeight;

    /** @var int */
    private $iMaxWidth;

    /** @var int */
    private $iMaxHeight;

    /** @var int */
    private $iQuality = 100;

    /** @var int */
    private $iCompression = 4;


    /**
     * @param string $sFile Full path to the image file.
     * @param int $iMaxWidth Default value 3000.
     * @param int $iMaxHeight Default value 3000.
     */
    public function __construct($sFile, $iMaxWidth = 3000, $iMaxHeight = 3000)
    {
        $this->sFile = $sFile;
        $this->iMaxWidth = $iMaxWidth;
        $this->iMaxHeight = $iMaxHeight;
    }

    /**
     * @return bool
     * @throws TooLargeException If the image file is not found.
     */
    public function validate()
    {
        $mImgType = $this->getType();

        if (!is_file($this->sFile) || !$mImgType) {
            if (isDebug()) {
                throw new TooLargeException('The file could not be uploaded. Possibly too large.');
            } else {
                return false;
            }
        } else {
            switch ($mImgType) {
                // JPG
                case static::JPG:
                    $this->rImage = imagecreatefromjpeg($this->sFile);
                    $this->sType = 'jpg';
                break;

                // PNG
                case static::PNG:
                    $this->rImage = imagecreatefrompng($this->sFile);
                    $this->sType = 'png';
                break;

                // GIF
                case static::GIF:
                    $this->rImage = imagecreatefromgif($this->sFile);
                    $this->sType = 'gif';
                break;

                case static::WEBP:
                    $this->rImage = imagecreatefromgif($this->sFile);
                    $this->sType = 'webp';
                break;

                // Invalid Zone
                default:
                    return false; // File type incompatible. Please save the image in .jpg, .png or .gif
            }

            $this->iWidth = imagesx($this->rImage);
            $this->iHeight = imagesy($this->rImage);

            // Automatic resizing if the image is too large
            if ($this->iWidth > $this->iMaxWidth OR $this->iHeight > $this->iMaxHeight) {
                $this->dynamicResize($this->iMaxWidth, $this->iMaxHeight);
            }

            return true;
        }
    }

    /**
     * @param int $iQ Devault value 100.
     * @return self
     */
    public function quality($iQ = 100)
    {
        $this->iQuality = $iQ;
        return $this;
    }

    /**
     * @param int $iC Devault value 4.
     * @return self
     */
    public function compression($iC = 4)
    {
        $this->iCompression = $iC;
        return $this;
    }

    /**
     * @param int $iX Default value null
     * @param int $iY Default value null
     * @return self
     */
    public function resize($iX = null, $iY = null)
    {
        if (!$iX) {
            // Width not given
            $iX = $this->iWidth * ($iY / $this->iHeight);
        } elseif (!$iY) {
            // Height not given
            $iY = $this->iHeight * ($iX / $this->iWidth);
        }

        $rTmp = imagecreatetruecolor($iX, $iY);
        imagecopyresampled($rTmp, $this->rImage, 0, 0, 0, 0, $iX, $iY, $this->iWidth, $this->iHeight);
        $this->rImage = &$rTmp;

        $this->iWidth = $iX;
        $this->iHeight = $iY;

        return $this;
    }

    /**
     * @param int $iX Default value 0.
     * @param int $iY Default value 0.
     * @param int $iWidth Default valie 1.
     * @param int $iHeight Default value 1.
     * @return self
     */
    public function crop($iX = 0, $iY = 0, $iWidth = 1, $iHeight = 1)
    {
        $rTmp = imagecreatetruecolor($iWidth, $iHeight);
        imagecopyresampled($rTmp, $this->rImage, 0, 0, $iX, $iY, $iWidth, $iHeight, $iWidth, $iHeight);
        $this->rImage = &$rTmp;

        $this->iWidth = $iWidth;
        $this->iHeight = $iHeight;

        return $this;
    }

    /**
     * @param int $iNewWidth
     * @param int $iNewHeight
     * @return self
     */
    public function dynamicResize($iNewWidth, $iNewHeight)
    {
        if ($iNewHeight > $iNewWidth OR ($iNewHeight == $iNewWidth AND $this->iHeight < $this->iWidth)) {
            // Taller image
            $this->resize(NULL, $iNewHeight);

            $iW = ($iNewWidth - $this->iWidth) / -2;
            $this->crop($iW, 0, $iNewWidth, $iNewHeight);
        } else {
            // Wider image
            $this->resize($iNewWidth, NULL);

            $iY = ($iNewHeight - $this->iHeight) / -2;
            $this->crop(0, $iY, $iNewWidth, $iNewHeight);
        }

        $this->iWidth = $iNewWidth;
        $this->iHeight = $iNewHeight;

        return $this;
    }

    /**
     * @param int $iSize
     * @see \PH7\Framework\Image\Image::dynamicResize() The method that is returned by this method.
     * @return self
     */
    public function square($iSize)
    {
        return $this->dynamicResize($iSize, $iSize);
    }

    /**
     * @param int $iWidth
     * @param int $iHeight
     * @param string $sZone Default value is center.
     * @see \PH7\Framework\Image\Image::crop() The method that is returned by this method.
     * @return self
     * @throws PH7InvalidArgumentException If the image crop is invalid.
     */
    public function zoneCrop($iWidth, $iHeight, $sZone = 'center')
    {
        switch ($sZone) {
            // Center
            case 'center':
                $iX = ($iWidth - $this->iWidth) / -2;
                $iY = ($iHeight - $this->iHeight) / -2;
                break;

            // Top Left
            case 'top-left':
                $iX = 0;
                $iY = 0;
                break;

            // Top
            case 'top':
                $iX = ($this->iWidth - $iWidth) / 2;
                $iY = 0;
                break;

            // Top Right
            case 'top-right':
                $iX = $this->iWidth - $iWidth;
                $iY = 0;
                break;

            // Right
            case 'right':
                $iX = $this->iWidth - $iWidth;
                $iY = ($this->iHeight - $iHeight) / 2;
                break;

            // Bottom Right
            case 'bottom-right':
                $iX = $this->iWidth - $iWidth;
                $iY = $this->iHeight - $iHeight;
                break;

            // Bottom
            case 'bottom':
                $iX = ($this->iWidth - $iWidth) / 2;
                $iY = $this->iHeight - $iHeight;
                break;

            // Bottom Left
            case 'bottom-left':
                $iX = 0;
                $iY = $this->iHeight - $iHeight;
                break;

            // Left
            case 'left':
                $iX = 0;
                $iY = ($this->iHeight - $iHeight) / 2;
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException('Invalid image crop zone ' . $sZone . ' given for image helper zoneCrop().');
        }

        return $this->crop($iX, $iY, $iWidth, $iHeight);
    }

    /**
     * @param int $iDeg Default value 0.
     * @param int $iBg Default value 0.
     * @return self
     */
    public function rotate($iDeg = 0, $iBg = 0)
    {
        $this->rImage = imagerotate($this->rImage, $iDeg, $iBg);
        return $this;
    }

    /**
     * Create a Watermark text.
     *
     * @param string $sText Text of watermark.
     * @param int $iSize The size of text. Between 0 to 5.
     * @return self
     */
     public function watermarkText($sText, $iSize)
     {
         $iWidthText = $this->iWidth-imagefontwidth($iSize)*mb_strlen($sText)-3;
         $iHeightText = $this->iHeight-imagefontheight($iSize)-3;

         $rWhite = imagecolorallocate($this->rImage, 255, 255, 255);
         $rBlack = imagecolorallocate($this->rImage, 0, 0, 0);
         $rGray = imagecolorallocate($this->rImage, 127, 127, 127);

         if ($iWidthText > 0 && $iHeightText > 0) {
             if (imagecolorat($this->rImage, $iWidthText, $iHeightText) > $rGray) {
                 $rColor = $rBlack;
             }
             if (imagecolorat($this->rImage, $iWidthText, $iHeightText) < $rGray) {
                 $rColor = $rWhite;
             }
         } else {
             $rColor = $rWhite;
         }

         imagestring($this->rImage, $iSize, $iWidthText-1, $iHeightText-1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText+1, $iHeightText+1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText-1, $iHeightText+1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText+1, $iHeightText-1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText-1, $iHeightText, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText+1, $iHeightText, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText, $iHeightText-1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText, $iHeightText+1, $sText, $rWhite-$rColor);
         imagestring($this->rImage, $iSize, $iWidthText, $iHeightText, $sText, $rColor);

         return $this;
     }

    /**
     * Save an image.
     *
     * @param string $sFile
     * @return self
     * @throws PH7InvalidArgumentException If the image format is invalid.
     */
    public function save($sFile)
    {
        switch ($this->sType) {
            // JPG
            case 'jpg':
                imagejpeg($this->rImage, $sFile, $this->iQuality);
                break;

            // PNG
            case 'png':
                imagepng($this->rImage, $sFile, $this->iCompression);
                break;

            // GIF
            case 'gif':
                imagegif($this->rImage, $sFile, $this->iQuality);
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException('Invalid format Image in method ' . __METHOD__ . ' of class ' . __CLASS__);
        }

        return $this;
    }

    /**
     * Show an image.
     *
     * @return self
     * @throws PH7InvalidArgumentException If the image format is invalid.
     */
    public function show()
    {
        switch ($this->sType) {
            // JPG
            case 'jpg':
                header('Content-type: image/jpeg');
                imagejpeg($this->rImage, null, $this->iQuality);
                break;

            // GIF
            case 'gif':
                header('Content-type: image/gif');
                imagegif($this->rImage, null, $this->iQuality);
                break;

            // PNG
            case 'png':
                header('Content-type: image/png');
                imagepng($this->rImage, null, $this->iCompression);
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException('Invalid format image in method ' . __METHOD__ . ' of class ' . __CLASS__);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->rImage;
    }

    /**
     * Determine and get the type of the image (even an unallowed image type) by reading the first bytes and checking its signature.
     *
     * @return string|bool When a correct signature is found, returns the appropriate value, FALSE otherwise.
     */
    public function getType()
    {
        return exif_imagetype($this->sFile);
    }

    /**
     * Get the image extension.
     *
     * @return string The extension of the image without the dot.
     */
    public function getExt()
    {
        return $this->sType;
    }

    /**
     * Remove the attributes, temporary file and memory resources.
     */
    public function __destruct()
    {
        // Remove the temporary image
        (new File)->deleteFile($this->sFile);

        // Free the memory associated with the image
        @imagedestroy($this->rImage);

        unset(
            $this->sFile,
            $this->sType,
            $this->rImage,
            $this->iWidth,
            $this->iHeight,
            $this->iMaxWidth,
            $this->iMaxHeight,
            $this->iQuality,
            $this->iCompression
        );
    }
}
