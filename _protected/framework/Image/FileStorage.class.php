<?php
/**
 * @desc             Class is used to create/manipulate images using GD library.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Image
 * @link             https://ph7.me
 * @linkGD           https://php.net/manual/book.image.php
 */

namespace PH7\Framework\Image;

defined('PH7') or exit('Restricted access');

use GdImage;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;
use PH7\Framework\File\TooLargeException;

class FileStorage implements Storageable
{
    public const MAX_FILENAME_LENGTH = 16;

    /*** Alias ***/
    public const JPG = IMAGETYPE_JPEG;
    public const PNG = IMAGETYPE_PNG;
    public const GIF = IMAGETYPE_GIF;
    public const WEBP = IMAGETYPE_WEBP;

    public const JPG_NAME = 'jpg';
    public const PNG_NAME = 'png';
    public const GIF_NAME = 'gif';
    public const WEBP_NAME = 'webp';

    public const SUPPORTED_TYPES = [
        self::JPG_NAME,
        self::PNG_NAME,
        self::GIF_NAME,
        self::WEBP_NAME
    ];

    // Available zone corps
    private const ZONE_CROP_CENTER = 'center';
    private const ZONE_CORP_TOP_LEFT = 'top-left';
    private const ZONE_CORP_TOP = 'top';
    private const ZONE_CORP_TOP_RIGHT = 'top-right';
    private const ZONE_CORP_RIGHT = 'right';
    private const ZONE_CORP_BOTTOM_RIGHT = 'bottom-right';
    private const ZONE_CORP_BOTTOM = 'bottom';
    private const ZONE_CORP_BOTTOM_LEFT = 'bottom-left';
    private const ZONE_CORP_LEFT = 'left';


    private const DEFAULT_MAX_WIDTH = 3000;
    private const DEFAULT_MAX_HEIGHT = 3000;

    private const DEFAULT_IMAGE_QUALITY = 100;
    private const DEFAULT_COMPRESSION_LEVEL = 4;

    /** @var string */
    private $sFile;

    /** @var string */
    private $sType;

    /** @var resource|GdImage|false */
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
    private $iQuality = self::DEFAULT_IMAGE_QUALITY;

    /** @var int */
    private $iCompression = self::DEFAULT_COMPRESSION_LEVEL;


    /**
     * @param string $sFile Full path to the image file.
     * @param int $iMaxWidth Default value 3000.
     * @param int $iMaxHeight Default value 3000.
     */
    public function __construct($sFile, $iMaxWidth = self::DEFAULT_MAX_WIDTH, $iMaxHeight = self::DEFAULT_MAX_HEIGHT)
    {
        $this->sFile = $sFile;
        $this->iMaxWidth = $iMaxWidth;
        $this->iMaxHeight = $iMaxHeight;
    }

    /**
     * @return bool
     *
     * @throws TooLargeException If the image file is not found.
     */
    public function validate()
    {
        $mImgType = $this->getType();

        if (!$mImgType || !is_file($this->sFile)) {
            if (isDebug()) {
                throw new TooLargeException('DebugMode: The file could not be uploaded. Possibly too large.');
            }
            return false;
        }

        switch ($mImgType) {
            case self::JPG:
                $this->rImage = imagecreatefromjpeg($this->sFile);
                $this->sType = self::JPG_NAME;
                break;

            case self::PNG:
                $this->rImage = imagecreatefrompng($this->sFile);
                $this->sType = self::PNG_NAME;
                break;

            case self::GIF:
                $this->rImage = imagecreatefromgif($this->sFile);
                $this->sType = self::GIF_NAME;
                break;

            case self::WEBP: // Will only work with PHP >= 7.1
                $this->rImage = imagecreatefromwebp($this->sFile);
                $this->sType = self::WEBP_NAME;
                break;

            // Invalid Zone
            default:
                return false; // File type incompatible. Please save the image in .jpg, .png or .gif
        }

        $this->iWidth = imagesx($this->rImage);
        $this->iHeight = imagesy($this->rImage);

        if ($this->isTooLarge()) {
            // Automatic resizing if the image is too large
            $this->dynamicResize($this->iMaxWidth, $this->iMaxHeight);
        }

        return true;
    }

    /**
     * @param int $iQ From 0 (worst quality) to 100 (best quality).
     *
     * @return self
     */
    public function quality($iQ = self::DEFAULT_IMAGE_QUALITY)
    {
        $this->iQuality = $iQ;

        return $this;
    }

    /**
     * @param int $iC
     *
     * @return self
     */
    public function compression($iC = self::DEFAULT_COMPRESSION_LEVEL)
    {
        $this->iCompression = $iC;

        return $this;
    }

    public function resize(?int $iX = null, ?int $iY = null): self
    {
        if (!$iX) { // If height is not given
            $iX = $this->iWidth * ($iY / $this->iHeight);
        } elseif (!$iY) { // If width is not given
            $iY = $this->iHeight * ($iX / $this->iWidth);
        }

        $rTmp = imagecreatetruecolor($iX, $iY);
        imagecopyresampled(
            $rTmp,
            $this->rImage,
            0,
            0,
            0,
            0,
            $iX,
            $iY,
            $this->iWidth,
            $this->iHeight
        );

        $this->rImage = &$rTmp;

        $this->iWidth = $iX;
        $this->iHeight = $iY;

        return $this;
    }

    /**
     * @param int $iX
     * @param int $iY
     * @param int $iWidth
     * @param int $iHeight
     *
     * @return self
     */
    public function crop($iX = 0, $iY = 0, $iWidth = 1, $iHeight = 1): self
    {
        $rTmp = imagecreatetruecolor($iWidth, $iHeight);
        imagecopyresampled(
            $rTmp,
            $this->rImage,
            0,
            0,
            $iX,
            $iY,
            $iWidth,
            $iHeight,
            $iWidth,
            $iHeight
        );
        $this->rImage = &$rTmp;

        $this->iWidth = $iWidth;
        $this->iHeight = $iHeight;

        $this->preserveTransparencies();

        return $this;
    }

    /**
     * @param int $iNewWidth
     * @param int $iNewHeight
     *
     * @return self
     */
    public function dynamicResize($iNewWidth, $iNewHeight)
    {
        if (
            $iNewHeight > $iNewWidth ||
            ($iNewHeight === $iNewWidth && $this->iHeight < $this->iWidth)
        ) {
            // Taller image
            $this->resize(null, $iNewHeight);

            $iW = ($iNewWidth - $this->iWidth) / -2;
            $this->crop((int)$iW, 0, $iNewWidth, $iNewHeight);
        } else {
            // Wider image
            $this->resize($iNewWidth, null);

            $iY = ($iNewHeight - $this->iHeight) / -2;
            $this->crop(0, (int)$iY, $iNewWidth, $iNewHeight);
        }

        $this->iWidth = $iNewWidth;
        $this->iHeight = $iNewHeight;

        return $this;
    }

    /**
     * @param int $iSize
     *
     * @see self::dynamicResize() The method that is returned by this method.
     *
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
     *
     * @see self::crop() The method that is returned by this method.
     *
     * @return self
     *
     * @throws PH7InvalidArgumentException If the image crop is invalid.
     */
    public function zoneCrop($iWidth, $iHeight, $sZone = self::ZONE_CROP_CENTER)
    {
        switch ($sZone) {
            case self::ZONE_CROP_CENTER:
                $iX = ($iWidth - $this->iWidth) / -2;
                $iY = ($iHeight - $this->iHeight) / -2;
                break;

            case self::ZONE_CORP_TOP_LEFT:
                $iX = 0;
                $iY = 0;
                break;

            case self::ZONE_CORP_TOP:
                $iX = ($this->iWidth - $iWidth) / 2;
                $iY = 0;
                break;

            case self::ZONE_CORP_TOP_RIGHT:
                $iX = $this->iWidth - $iWidth;
                $iY = 0;
                break;

            // Right
            case self::ZONE_CORP_RIGHT:
                $iX = $this->iWidth - $iWidth;
                $iY = ($this->iHeight - $iHeight) / 2;
                break;

            case self::ZONE_CORP_BOTTOM_RIGHT:
                $iX = $this->iWidth - $iWidth;
                $iY = $this->iHeight - $iHeight;
                break;

            case self::ZONE_CORP_BOTTOM:
                $iX = ($this->iWidth - $iWidth) / 2;
                $iY = $this->iHeight - $iHeight;
                break;

            case self::ZONE_CORP_BOTTOM_LEFT:
                $iX = 0;
                $iY = $this->iHeight - $iHeight;
                break;

            case self::ZONE_CORP_LEFT:
                $iX = 0;
                $iY = ($this->iHeight - $iHeight) / 2;
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException(
                    'Invalid image crop zone ' . $sZone . ' given for image helper zoneCrop().'
                );
        }

        return $this->crop($iX, $iY, $iWidth, $iHeight);
    }

    /**
     * @param int $iDeg
     * @param int $iBg
     *
     * @return self
     */
    public function rotate($iDeg = 0, $iBg = 0)
    {
        $this->rImage = imagerotate($this->rImage, $iDeg, $iBg);
        return $this;
    }

    /**
     * Create a Watermark text on the image.
     *
     * @param string $sText Text of watermark.
     * @param int $iSize The size of text. Between 0 to 5.
     *
     * @return self
     */
    public function watermarkText($sText, $iSize)
    {
        $iWidthText = $this->iWidth - imagefontwidth($iSize) * mb_strlen($sText) - 3;
        $iHeightText = $this->iHeight - imagefontheight($iSize) - 3;

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

        imagestring($this->rImage, $iSize, $iWidthText - 1, $iHeightText - 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText + 1, $iHeightText + 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText - 1, $iHeightText + 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText + 1, $iHeightText - 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText - 1, $iHeightText, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText + 1, $iHeightText, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText, $iHeightText - 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText, $iHeightText + 1, $sText, $rWhite - $rColor);
        imagestring($this->rImage, $iSize, $iWidthText, $iHeightText, $sText, $rColor);

        return $this;
    }

    /**
     * Save an image.
     *
     * @param string $sFile
     *
     * @return self
     *
     * @throws PH7InvalidArgumentException If the image format is invalid.
     */
    public function save(string $sFile): self
    {
        switch ($this->sType) {
            case self::JPG_NAME:
                imagejpeg($this->rImage, $sFile, $this->iQuality);
                break;

            case self::PNG_NAME:
                imagepng($this->rImage, $sFile, $this->iCompression);
                break;

            case self::GIF_NAME:
                imagegif($this->rImage, $sFile, $this->iQuality);
                break;

            case self::WEBP_NAME:
                imagewebp($this->rImage, $sFile, $this->iQuality);
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException(
                    'Invalid format Image in method ' . __METHOD__ . ' of class ' . __CLASS__
                );
        }

        return $this;
    }

    /**
     * Show an image.
     *
     * @return self
     *
     * @throws PH7InvalidArgumentException If the image format is invalid.
     */
    public function show()
    {
        $this->preserveTransparencies();

        switch ($this->sType) {
            case self::JPG_NAME:
                header('Content-type: image/jpeg');
                imagejpeg($this->rImage, null, $this->iQuality);
                break;

            case self::PNG_NAME:
                header('Content-type: image/png');
                imagepng($this->rImage, null, $this->iCompression);
                break;

            case self::GIF_NAME:
                header('Content-type: image/gif');
                imagegif($this->rImage, null, $this->iQuality);
                break;

            case self::WEBP_NAME:
                header('Content-type: image/webp');
                imagewebp($this->rImage, null, $this->iQuality);
                break;

            // Invalid Zone
            default:
                throw new PH7InvalidArgumentException(
                    'Invalid format image in method ' . __METHOD__ . ' of class ' . __CLASS__
                );
        }

        return $this;
    }

    public function remove(string $sFile): self
    {
        // If it exists, remove the temporary image file
        (new File)->deleteFile($sFile);

        // Free the memory associated with the image
        // Make sure $rImage is the correct type and not null. Needs to be an instance of GdImage
        if ($this->rImage instanceof GdImage) {
            @imagedestroy($this->rImage);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return substr($this->sFile, 0, self::MAX_FILENAME_LENGTH);
    }

    /**
     * Determine and get the type of the image (even an unallowed image type) by reading the first bytes and checking its signature.
     *
     * @return int|bool When a correct signature is found, returns the appropriate integer constant value, FALSE otherwise.
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
     * @return bool
     */
    public function isTransparent()
    {
        $mTransparentIndex = $this->getTransparentColor();

        return $mTransparentIndex >= 0;
    }

    /**
     * @return false|int Returns the identifier of the transparent color index.
     */
    public function getTransparentColor()
    {
        return imagecolortransparent($this->rImage);
    }

    private function preserveTransparencies()
    {
        switch ($this->sType) {
            case self::PNG_NAME:
                $this->allocateAlphaColorTransparency();
                $this->handlePngTransparency();
                break;

            case self::GIF_NAME:
                $this->allocateAlphaColorTransparency();
                imagealphablending($this->rImage, true);
                break;

            case self::JPG_NAME:
                imagealphablending($this->rImage, true);
                break;
        }
    }

    /**
     * Create a new transparent alpha color.
     */
    private function allocateAlphaColorTransparency()
    {
        $iAlphaColor = imagecolorallocatealpha($this->rImage, 0, 0, 0, 127);
        imagefill($this->rImage, 0, 0, $iAlphaColor);
    }

    private function handlePngTransparency()
    {
        // Turn off (temporarily) transparency blending
        imagealphablending($this->rImage, false);

        // Restore transparency blending
        imagesavealpha($this->rImage, true);
    }

    /**
     * @return bool TRUE if the image is too large (and should be resized), FALSE otherwise.
     */
    private function isTooLarge()
    {
        return $this->iWidth > $this->iMaxWidth || $this->iHeight > $this->iMaxHeight;
    }

    /**
     * Remove temporary file.
     */
    public function __destruct()
    {
        $this->remove($this->sFile);
    }
}
