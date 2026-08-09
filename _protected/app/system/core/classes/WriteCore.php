<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\File;

abstract class WriteCore
{
    public const THUMBNAIL_FILENAME = 'thumb.png';
    public const DEFAULT_THUMBNAIL_FILENAME = 'default_thumb.jpg';

    private const BLOG_NAME = 'blog';
    private const NOTE_NAME = 'note';

    private const ALLOWED_MODULES = [
        self::BLOG_NAME,
        self::NOTE_NAME
    ];

    /**
     * @param int|string $mId  put the username + the PH7_DS constant + the image file for the Note module or just the post ID for the Blog module
     * @param string     $sMod Module name. Choose between 'blog' and 'note'.
     */
    public function deleteThumb(int|string $mId, string $sMod, File $oFile): bool
    {
        self::checkMod($sMod);

        $sThumbPath = self::normalizeThumbPath($mId, $sMod);
        if ($sThumbPath === null) {
            return false;
        }

        return $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sMod . PH7_DS . PH7_IMG . $sThumbPath);
    }

    /**
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     *
     * @throws PH7InvalidArgumentException if the module is incorrect
     */
    public static function checkMod($sMod): void
    {
        if (!in_array($sMod, self::ALLOWED_MODULES, true)) {
            throw new PH7InvalidArgumentException(sprintf('Wrong module: %s', $sMod));
        }
    }

    private static function normalizeThumbPath(int|string $mId, string $sMod): ?string
    {
        if ($sMod === self::BLOG_NAME) {
            $sId = (string)$mId;
            if (!ctype_digit($sId) || (int)$sId <= 0 || (string)(int)$sId !== $sId) {
                return null;
            }

            return $sId . PH7_DS . static::THUMBNAIL_FILENAME;
        }

        if (!is_string($mId)) {
            return null;
        }

        $aParts = preg_split('~[\\\\/]~', $mId);
        if (!is_array($aParts) || count($aParts) !== 2) {
            return null;
        }

        [$sUsername, $sThumbnail] = $aParts;
        if (
            $sUsername === ''
            || $sThumbnail === ''
            || File::getFileBasename($sUsername) !== $sUsername
            || File::getFileBasename($sThumbnail) !== $sThumbnail
        ) {
            return null;
        }

        return $sUsername . PH7_DS . $sThumbnail;
    }
}
