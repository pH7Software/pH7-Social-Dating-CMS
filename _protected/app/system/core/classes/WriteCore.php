<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
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
     * @param int|string $mId Put the username + the PH7_DS constant + the image file for the Note module or just the post ID for the Blog module.
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     * @param File $oFile
     */
    public function deleteThumb(int|string $mId, string $sMod, File $oFile): bool
    {
        self::checkMod($sMod);

        if ($sMod === self::BLOG_NAME) {
            $mId .= PH7_DS . static::THUMBNAIL_FILENAME;
        }

        return $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sMod . PH7_DS . PH7_IMG . $mId);
    }

    /**
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     *
     * @throws PH7InvalidArgumentException If the module is incorrect.
     */
    public static function checkMod($sMod): void
    {
        if (!in_array($sMod, self::ALLOWED_MODULES, true)) {
            throw new PH7InvalidArgumentException(
                sprintf('Wrong module: %s', $sMod)
            );
        }
    }
}
