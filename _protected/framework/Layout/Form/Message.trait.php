<?php
/**
 * Helper that gives popular predefined form messages to avoid duplicating same strings over and over again.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Form
 */

declare(strict_types=1);

namespace PH7\Framework\Layout\Form;

use PH7\Framework\Image\FileStorage as FileStorageImage;
use PH7\Framework\Video\Video;

trait Message
{
    /**
     * Launch Error Token Message.
     */
    public static function errorTokenMsg(): string
    {
        return t('The security token does not exist or its lifetime has expired. Please try once again');
    }

    /**
     * Launch Wrong Image File Type Message.
     */
    public static function wrongImgFileTypeMsg(): string
    {
        return t('The file type is incompatible or too large. Please try with a smaller image with one of the following extensions: %0%', self::getImageExtensions());
    }

    /**
     * Launch Wrong Video File Type Message.
     */
    public static function wrongVideoFileTypeMsg(): string
    {
        return t('File type is incompatible or too large. The accepted file types are: %0%', self::getVideoExtensions());
    }

    /**
     * Launch Wrong HTTP Request Method Message.
     */
    public static function wrongRequestMethodMsg(string $sMethodName): string
    {
        return t('The HTTP parameter must be a %0% type!', $sMethodName);
    }

    /**
     * Launch an Error Sending Email.
     */
    public static function errorSendingEmail(): string
    {
        return t('Oops! Our email server encountered an internal error. The email could not be sent. Please try again later');
    }

    /**
     * Number of connection attempts exceeded.
     *
     * @param int $iWaitTime (in minutes)
     */
    public static function loginAttemptsExceededMsg(int $iWaitTime): string
    {
        return t('Oops! You have exceeded the allowed login attempts. Please try again in %0% %1%.', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    public static function duplicateContentMsg(): string
    {
        return t("It seems you previously sent the same message. Be unique and you'll increase your chances of receiving a reply 😉");
    }

    public static function tooManyUrlsMsg(): string
    {
        return t('Oops! It seems you are abusing of links. Why are links so important to you?');
    }

    public static function tooManyEmailsMsg(): string
    {
        return t('Oops! It seems you abused of emails. Why are emails so important to you?');
    }


    /**
     * Wait to write a new message (mainly to reduce spam).
     */
    public static function waitWriteMsg(int $iWaitTime): string
    {
        return t('Oops! Please wait %0% %1% before sending another one 😉', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    /**
     * Wait to new registration (mainly to reduce spam).
     *
     * @param int $iWaitTime (in minutes)
     */
    public static function waitRegistrationMsg(int $iWaitTime): string
    {
        return t('Oops! Somebody has recently registered with the same IP address. Do you mind waiting %0% %1%?', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    private static function getTimeText(int $iWaitTime): string
    {
        $iWaitTime = (int)$iWaitTime;

        return $iWaitTime < 2 ? t('minute') : ($iWaitTime < 60 ? t('minutes') : ($iWaitTime < 120 ? t('hour') : ($iWaitTime < 1440 ? t('hours') : ($iWaitTime < 2880 ? t('day') : t('days')))));
    }

    /**
     * Conversion time if necessary (we do not do the conversion of minutes so you should rather take sharp hours (same thing for days).
     *
     * @param int $iWaitTime (in minutes)
     */
    private static function convertTime(int $iWaitTime): int
    {
        if ($iWaitTime > 60) {
            $iDivide = ($iWaitTime < 1440) ? 60 : 1440;

            // Cast float value from `floor()` to integer
            $iWaitTime = (int)floor($iWaitTime / $iDivide);
        }

        return $iWaitTime;
    }

    /**
     * @return string e.g., .jpg, .png, .gif, .webp
     */
    private static function getImageExtensions(): string
    {
        return '.' . implode(', .', FileStorageImage::SUPPORTED_TYPES);
    }

    /**
     * @return string e.g., .mov, .avi, .flv, .mp4, .mpg
     */
    private static function getVideoExtensions(): string
    {
        return '.' . implode(', .', array_keys(Video::SUPPORTED_TYPES));
    }
}
